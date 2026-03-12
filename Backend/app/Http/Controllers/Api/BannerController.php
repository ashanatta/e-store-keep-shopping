<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Public – return all active banners.
     * Admin route returns every banner regardless of is_active.
     */
    public function index(Request $request)
    {
        $query = Banner::latest();

        if (! $request->boolean('all')) {
            $query->where('is_active', true);
        }

        return response()->json($query->get());
    }

    /**
     * Admin – create a new banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $validated['image']     = $this->uploadImage($request->file('image'), 'banners');
        $validated['is_active'] = $request->boolean('is_active', true);

        $banner = Banner::create($validated);

        return response()->json($banner, 201);
    }

    /**
     * Admin – update an existing banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImageIfRemote($banner->image);
            $validated['image'] = $this->uploadImage($request->file('image'), 'banners');
        }

        $validated['is_active'] = $request->boolean('is_active', $banner->is_active);

        $banner->update($validated);

        return response()->json($banner);
    }

    /**
     * Admin – delete a banner and its image.
     */
    public function destroy(Banner $banner)
    {
        $this->deleteImageIfRemote($banner->image);
        $banner->delete();

        return response()->json(null, 204);
    }

    /**
     * Upload to Cloudinary when CLOUDINARY_URL is set, otherwise fall back to local disk.
     */
    private function uploadImage(UploadedFile $file, string $folder): string
    {
        if (env('CLOUDINARY_URL')) {
            $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'e-store/' . $folder,
            ]);

            return $result['secure_url'] ?? $result['url'];
        }

        return $file->store($folder, 'public');
    }

    /**
     * Delete Cloudinary image (skip local paths – they aren't managed here).
     */
    private function deleteImageIfRemote(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (str_starts_with($path, 'http')) {
            // Extract the public_id from the Cloudinary URL and destroy it
            // URL format: https://res.cloudinary.com/<cloud>/image/upload/v.../e-store/banners/<id>
            if (str_contains($path, 'cloudinary.com')) {
                preg_match('/\/upload\/(?:v\d+\/)?(.+?)(?:\.\w+)?$/', $path, $matches);
                if (! empty($matches[1])) {
                    try {
                        cloudinary()->uploadApi()->destroy($matches[1]);
                    } catch (\Exception) {
                        // Silently ignore if deletion fails
                    }
                }
            }
        } else {
            Storage::disk('public')->delete($path);
        }
    }
}
