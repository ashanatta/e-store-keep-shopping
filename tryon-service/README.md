# Virtual Try-On Service

Small Python (FastAPI) service behind the "Try it on with AI" button on the product page.
It runs the open-source [IDM-VTON](https://huggingface.co/spaces/yisol/IDM-VTON) model for free
through its public Hugging Face Space, so no paid API key and no GPU are needed.

```
Browser → Laravel /api/try-on → this service (127.0.0.1:8001) → Hugging Face IDM-VTON
```

## Run it

Windows: double-click `start.bat` (the first run installs everything), or:

```bash
python -m venv .venv
.venv\Scripts\python -m pip install -r requirements.txt
.venv\Scripts\python -m uvicorn app:app --host 127.0.0.1 --port 8001
```

Check it's up: http://127.0.0.1:8001/health

Laravel finds it through `TRYON_SERVICE_URL` in `Backend/.env` (default `http://127.0.0.1:8001`).
If the service isn't running, the site shows "Virtual try-on is not available right now."

## Optional settings (environment variables)

| Variable | Default | Purpose |
| --- | --- | --- |
| `HF_TOKEN` | – | Free Hugging Face token (huggingface.co → Settings → Access Tokens, "Read"). Gives a bigger free GPU quota than anonymous use. |
| `TRYON_SECRET` | – | Shared secret. If set, also set `TRYON_SERVICE_SECRET` to the same value in `Backend/.env`. Use this whenever the service is reachable from outside your machine. |
| `TRYON_SPACE` | `yisol/IDM-VTON` | Hugging Face Space to use. |
| `TRYON_MAX_WORKERS` | `2` | Try-ons processed at the same time. |
| `TRYON_JOB_TIMEOUT` | `240` | Seconds before a job is given up. |

## Limits of the free model

- Hugging Face gives each user/IP a limited amount of free GPU time per day. When it runs out,
  customers see "The free AI limit has been reached". An `HF_TOKEN` raises the limit.
- The Space is run by a third party and can be slow, busy or offline.
- IDM-VTON is trained on upper-body clothing: shirts, t-shirts, tops, kurtas and jackets work best.
- Customer photos are sent to Hugging Face. Nothing is stored by this service; jobs live in memory for at most an hour.

## API

- `POST /run` `{"person_image": "<data URI or URL>", "garment_image": "<data URI or URL>", "description": "..."}` → `202 {"id": "..."}`
- `GET /status/{id}` → `{"status": "processing"}` | `{"status": "completed", "image": "data:image/jpeg;base64,..."}` | `{"status": "failed", "error": {"name": "...", "message": "..."}}`
