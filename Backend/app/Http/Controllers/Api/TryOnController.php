<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * AI virtual try-on.
 *
 * Proxies to the Python try-on service in /tryon-service, which runs the free
 * IDM-VTON model on Hugging Face. The flow is asynchronous: store() starts a
 * job and returns its id, then the frontend polls show() until it's done.
 * Keeping each request short matters because `php artisan serve` handles one
 * request at a time — a long blocking call would freeze the whole app.
 *
 * Nothing is persisted: the customer's photo is forwarded to the service and
 * the result is returned as a data URI straight to the browser.
 */
class TryOnController extends Controller
{
    /**
     * Start a try-on job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'garment_image' => 'required|string|max:2048',
            'person_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        $product = Product::with('variants.images')->findOrFail($validated['product_id']);
        $garmentInput = $this->resolveGarmentInput($product, $validated['garment_image']);

        $personFile = $request->file('person_image');
        $personInput = $this->toDataUri($personFile->getRealPath(), $personFile->getMimeType());

        try {
            $response = $this->service()->timeout(30)->post('/run', [
                'person_image' => $personInput,
                'garment_image' => $garmentInput,
                'description' => mb_substr($product->name, 0, 200),
            ]);
        } catch (ConnectionException $e) {
            Log::warning('Try-on service unreachable. Is tryon-service running?', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Virtual try-on is not available right now.'], 503);
        }

        $jobId = $response->json('id');

        if ($response->failed() || ! $jobId) {
            Log::warning('Try-on service run failed', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            return response()->json(['message' => 'Could not start the try-on. Please try again.'], 502);
        }

        // Remember who owns this job so other users can't poll it.
        Cache::put($this->cacheKey($jobId), $request->user()->id, now()->addHour());

        return response()->json(['id' => $jobId], 202);
    }

    /**
     * Poll a job's status.
     */
    public function show(Request $request, string $id)
    {
        $ownerId = Cache::get($this->cacheKey($id));
        if ($ownerId === null || (int) $ownerId !== (int) $request->user()->id) {
            abort(404);
        }

        try {
            $response = $this->service()->timeout(15)->get('/status/' . rawurlencode($id));
        } catch (ConnectionException $e) {
            Log::warning('Try-on service unreachable during status check', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Virtual try-on is not available right now.'], 503);
        }

        if ($response->failed()) {
            Log::warning('Try-on status failed', ['status' => $response->status(), 'id' => $id]);
            Cache::forget($this->cacheKey($id));

            return response()->json([
                'status' => 'failed',
                'message' => 'The try-on was interrupted. Please try again.',
            ]);
        }

        $status = $response->json('status');

        if ($status === 'completed') {
            Cache::forget($this->cacheKey($id));

            return response()->json([
                'status' => 'completed',
                'image' => $response->json('image'),
            ]);
        }

        if ($status === 'failed') {
            Cache::forget($this->cacheKey($id));
            Log::info('Try-on job failed', ['id' => $id, 'error' => $response->json('error')]);

            return response()->json([
                'status' => 'failed',
                'message' => $this->friendlyError($response->json('error.name')),
            ]);
        }

        return response()->json(['status' => 'processing']);
    }

    private function service(): PendingRequest
    {
        $request = Http::baseUrl(config('services.tryon.url'))
            ->acceptJson()
            ->asJson();

        if ($secret = config('services.tryon.secret')) {
            $request->withHeaders(['X-Tryon-Secret' => $secret]);
        }

        return $request;
    }

    /**
     * Only allow images that actually belong to the product, so the endpoint
     * can't be used to run try-ons on arbitrary URLs.
     */
    private function resolveGarmentInput(Product $product, string $garmentImage): string
    {
        $allowed = collect([$product->image])
            ->merge($product->variants->flatMap(fn ($variant) => $variant->images->pluck('path')))
            ->filter()
            ->unique();

        if (! $allowed->contains($garmentImage)) {
            throw ValidationException::withMessages([
                'garment_image' => ['The selected image does not belong to this product.'],
            ]);
        }

        // Cloudinary images are public, the service can download them directly.
        if (str_starts_with($garmentImage, 'http://') || str_starts_with($garmentImage, 'https://')) {
            return $garmentImage;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($garmentImage)) {
            throw ValidationException::withMessages([
                'garment_image' => ['The product image could not be found.'],
            ]);
        }

        $path = $disk->path($garmentImage);

        return $this->toDataUri($path, mime_content_type($path) ?: 'image/jpeg');
    }

    private function toDataUri(string $path, ?string $mimeType): string
    {
        return 'data:' . ($mimeType ?: 'image/jpeg') . ';base64,' . base64_encode(file_get_contents($path));
    }

    private function friendlyError(?string $errorName): string
    {
        return match ($errorName) {
            'PoseError' => 'We couldn\'t detect your body clearly. Try a front-facing photo showing your upper body or full body.',
            'ImageLoadError' => 'One of the images could not be read. Please try a different photo.',
            'QuotaError' => 'The free AI limit has been reached for now. Please try again later.',
            'TimeoutError' => 'The AI is busy right now and took too long. Please try again in a few minutes.',
            'UnavailableError' => 'The AI model is temporarily unavailable. Please try again later.',
            default => 'The try-on couldn\'t be generated. Please try again with a different photo.',
        };
    }

    private function cacheKey(string $jobId): string
    {
        return 'tryon:' . $jobId;
    }
}
