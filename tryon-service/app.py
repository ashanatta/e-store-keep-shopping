"""
Keep-Shopping virtual try-on service.

A small FastAPI app that runs AI virtual try-ons for free through the public
IDM-VTON Hugging Face Space (https://huggingface.co/spaces/yisol/IDM-VTON).
The Laravel backend talks to this service; the browser never does.

    POST /run            start a try-on, returns {"id": "..."}
    GET  /status/{id}    {"status": "processing" | "completed" | "failed", ...}

Jobs run in a background thread pool so every HTTP request returns quickly.
Nothing is written to disk except short-lived temp files, which are deleted
as soon as a job finishes. Jobs are kept in memory for one hour.
"""

import base64
import binascii
import io
import logging
import os
import secrets
import shutil
import tempfile
import threading
import time
import uuid
from concurrent.futures import ThreadPoolExecutor
from pathlib import Path

import httpx
from fastapi import FastAPI, Header, HTTPException
from gradio_client import Client, handle_file
from PIL import Image, ImageOps
from pydantic import BaseModel, Field

SPACE = os.getenv("TRYON_SPACE", "yisol/IDM-VTON")
HF_TOKEN = os.getenv("HF_TOKEN") or None
SECRET = os.getenv("TRYON_SECRET") or None
MAX_WORKERS = int(os.getenv("TRYON_MAX_WORKERS", "2"))
JOB_TIMEOUT_SECONDS = int(os.getenv("TRYON_JOB_TIMEOUT", "240"))
DOWNLOAD_TIMEOUT_SECONDS = 120
JOB_TTL_SECONDS = 3600
MAX_INPUT_BYTES = 10 * 1024 * 1024

logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s")
log = logging.getLogger("tryon")

app = FastAPI(title="Keep-Shopping Try-On Service")
executor = ThreadPoolExecutor(max_workers=MAX_WORKERS)

jobs: dict[str, dict] = {}
jobs_lock = threading.Lock()

_client: Client | None = None
_client_lock = threading.Lock()


class TryOnError(Exception):
    def __init__(self, name: str, message: str):
        super().__init__(message)
        self.name = name
        self.message = message


class RunRequest(BaseModel):
    # Data URI ("data:image/jpeg;base64,...") or a public http(s) URL
    person_image: str = Field(min_length=1)
    garment_image: str = Field(min_length=1)
    description: str = Field(default="a garment", max_length=200)


def check_secret(provided: str | None) -> None:
    if SECRET and not (provided and secrets.compare_digest(provided, SECRET)):
        raise HTTPException(status_code=401, detail="Unauthorized")


def get_client() -> Client:
    global _client
    with _client_lock:
        if _client is None:
            log.info("Connecting to Hugging Face Space %s", SPACE)
            # download_files=False: we fetch only the result image ourselves,
            # with a longer timeout than gradio_client's default.
            _client = Client(
                SPACE,
                token=HF_TOKEN,
                verbose=False,
                download_files=False,
                httpx_kwargs={"timeout": DOWNLOAD_TIMEOUT_SECONDS},
            )
        return _client


def download_result(output) -> bytes:
    """Fetch the generated image from the Space, retrying slow downloads."""
    url = output.get("url") if isinstance(output, dict) else output
    if not url:
        raise TryOnError("PipelineError", "The AI returned no image.")

    if not url.startswith(("http://", "https://")):
        return Path(url).read_bytes()

    headers = {"Authorization": f"Bearer {HF_TOKEN}"} if HF_TOKEN else {}
    last_error: Exception | None = None
    for attempt in range(3):
        try:
            response = httpx.get(url, headers=headers, timeout=DOWNLOAD_TIMEOUT_SECONDS, follow_redirects=True)
            response.raise_for_status()
            return response.content
        except httpx.HTTPError as exc:
            last_error = exc
            log.info("Result download attempt %d failed: %s", attempt + 1, exc)
            time.sleep(2)
    raise TryOnError("UnavailableError", f"Could not download the result: {last_error}")


def reset_client() -> None:
    global _client
    with _client_lock:
        _client = None


def prepare_image(value: str, workdir: Path, name: str) -> str:
    """Turn a data URI into a normalised JPEG file. URLs are passed through."""
    if value.startswith(("http://", "https://")):
        return value

    if not value.startswith("data:image/") or ";base64," not in value:
        raise TryOnError("ImageLoadError", f"{name} must be an image data URI or URL.")

    try:
        raw = base64.b64decode(value.split(";base64,", 1)[1], validate=True)
    except (binascii.Error, ValueError):
        raise TryOnError("ImageLoadError", f"{name} is not valid base64.")

    if len(raw) > MAX_INPUT_BYTES:
        raise TryOnError("ImageLoadError", f"{name} is too large.")

    try:
        with Image.open(io.BytesIO(raw)) as img:
            img = ImageOps.exif_transpose(img).convert("RGB")
            path = workdir / f"{name}.jpg"
            img.save(path, "JPEG", quality=92)
    except Exception:
        raise TryOnError("ImageLoadError", f"{name} could not be read as an image.")

    return str(path)


def to_jpeg_data_uri(data: bytes) -> str:
    with Image.open(io.BytesIO(data)) as img:
        buffer = io.BytesIO()
        img.convert("RGB").save(buffer, "JPEG", quality=90)
    return "data:image/jpeg;base64," + base64.b64encode(buffer.getvalue()).decode()


def classify_error(exc: Exception) -> TryOnError:
    text = str(exc).lower()
    if isinstance(exc, TryOnError):
        return exc
    if "quota" in text or "exceeded" in text or "too many" in text:
        return TryOnError("QuotaError", "Free GPU quota reached.")
    if "pose" in text or "keypoint" in text or "no person" in text:
        return TryOnError("PoseError", "Could not detect a person in the photo.")
    if isinstance(exc, TimeoutError) or "timeout" in text or "timed out" in text:
        return TryOnError("TimeoutError", "The AI took too long to respond.")
    if "runtime_error" in text or "sleeping" in text or "invalid state" in text or "connect" in text:
        return TryOnError("UnavailableError", "The free AI model is currently unavailable.")
    return TryOnError("PipelineError", "The try-on could not be generated.")


def run_job(job_id: str, request: RunRequest) -> None:
    workdir = Path(tempfile.mkdtemp(prefix="tryon-"))
    started = time.time()
    try:
        person = prepare_image(request.person_image, workdir, "person")
        garment = prepare_image(request.garment_image, workdir, "garment")

        job = get_client().submit(
            dict={"background": handle_file(person), "layers": [], "composite": None},
            garm_img=handle_file(garment),
            garment_des=request.description or "a garment",
            is_checked=True,        # auto-generate the clothing mask
            is_checked_crop=True,   # auto-crop/resize arbitrary customer photos
            denoise_steps=30,
            seed=42,
            api_name="/tryon",
        )
        output = job.result(timeout=JOB_TIMEOUT_SECONDS)

        image = to_jpeg_data_uri(download_result(output[0]))
        update_job(job_id, status="completed", image=image)
        log.info("Job %s completed in %.1fs", job_id, time.time() - started)
    except Exception as exc:  # noqa: BLE001 - every failure must end the job
        error = classify_error(exc)
        if error.name in ("UnavailableError", "PipelineError"):
            reset_client()
        log.warning("Job %s failed (%s): %s", job_id, error.name, exc)
        update_job(job_id, status="failed", error={"name": error.name, "message": error.message})
    finally:
        shutil.rmtree(workdir, ignore_errors=True)


def update_job(job_id: str, **fields) -> None:
    with jobs_lock:
        if job_id in jobs:
            jobs[job_id].update(fields)


def purge_old_jobs() -> None:
    cutoff = time.time() - JOB_TTL_SECONDS
    with jobs_lock:
        for job_id in [k for k, v in jobs.items() if v["created_at"] < cutoff]:
            del jobs[job_id]


@app.get("/health")
def health():
    return {"ok": True, "space": SPACE}


@app.post("/run", status_code=202)
def run(request: RunRequest, x_tryon_secret: str | None = Header(default=None)):
    check_secret(x_tryon_secret)
    purge_old_jobs()

    job_id = uuid.uuid4().hex
    with jobs_lock:
        jobs[job_id] = {"status": "processing", "created_at": time.time()}

    executor.submit(run_job, job_id, request)
    return {"id": job_id}


@app.get("/status/{job_id}")
def status(job_id: str, x_tryon_secret: str | None = Header(default=None)):
    check_secret(x_tryon_secret)

    with jobs_lock:
        job = jobs.get(job_id)
        if job is None:
            raise HTTPException(status_code=404, detail="Job not found")
        payload = {k: v for k, v in job.items() if k != "created_at"}
        # Results are handed out once, then forgotten.
        if job["status"] != "processing":
            del jobs[job_id]

    return payload
