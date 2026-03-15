<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category =Category::select('id','name', 'image')->get();
        return response()->json($category, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' =>'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $data = [
            'name'=>$request->name,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'categories');
        }

        $category = Category::create($data);
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::select('id','name', 'image')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' =>'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $data = [
            'name'=>$request->name,
        ];

        if ($request->hasFile('image')) {
            $this->deleteImageIfRemote($category->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'categories');
        }

        $category->update($data);
        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $this->deleteImageIfRemote($category->image);
        $category->delete();
        return response()->json(['message' => 'Category deleted'], 200);
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
