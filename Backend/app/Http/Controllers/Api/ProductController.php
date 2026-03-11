<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Product::with('category:id,name', 'variants.color', 'variants.size', 'variants.images')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changed to file validation
            'variants' => 'required|json',
            'variant_images' => 'nullable|array',
            'variant_images.*' => 'nullable|array',
            'variant_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);
        $variantsPayload = $validatedData['variants'] ?? null;
        unset($validatedData['variants']);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $this->uploadImage($request->file('image'), 'products');
        }

        $product = Product::create($validatedData);

        if ($variantsPayload) {
            $variants = json_decode($variantsPayload, true);
            if (is_array($variants)) {
                $this->validateVariantsPayload($variants);
                $this->syncVariantsWithImages($request, $product, $variants);
            } else {
                throw ValidationException::withMessages([
                    'variants' => ['Variants payload is invalid.'],
                ]);
            }
        }

        return response()->json($product->load('category:id,name', 'variants.color', 'variants.size', 'variants.images'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Debugging: Log server time and product sale details
        Log::info('ProductController@show Debug:', [
            'Server Time (Carbon::now())' => \Carbon\Carbon::now()->toDateTimeString(),
            'Product ID' => $product->id,
            'Sale Start' => $product->sale_start ? $product->sale_start->toDateTimeString() : 'N/A',
            'Sale End' => $product->sale_end ? $product->sale_end->toDateTimeString() : 'N/A',
            'Discount Percentage' => $product->discount_percentage,
            'Calculated Final Price' => $product->final_price, // Accessor will be called here
        ]);

        return response()->json(
            $product->load('category:id,name', 'variants.color', 'variants.size', 'variants.images')
                ->loadCount('reviews')
                ->loadAvg('reviews', 'rating')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changed to file validation
            'variants' => 'required|json',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'variant_images' => 'nullable|array',
            'variant_images.*' => 'nullable|array',
            'variant_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);
        // Explicitly set sale_start and sale_end to null if they are empty strings
        if (isset($validatedData['sale_start']) && $validatedData['sale_start'] === '') {
            $validatedData['sale_start'] = null;
        }
        if (isset($validatedData['sale_end']) && $validatedData['sale_end'] === '') {
            $validatedData['sale_end'] = null;
        }
        $variantsPayload = $validatedData['variants'] ?? null;
        unset($validatedData['variants']);

        // Explicitly assign sale-related fields
        $product->discount_percentage = $validatedData['discount_percentage'] ?? 0;
        $product->sale_start = $validatedData['sale_start'] ?? null;
        $product->sale_end = $validatedData['sale_end'] ?? null;

        // Remove these fields from validatedData to prevent mass assignment conflicts
        unset($validatedData['discount_percentage']);
        unset($validatedData['sale_start']);
        unset($validatedData['sale_end']);

        if ($request->hasFile('image')) {
            $this->deleteImageIfLocal($product->image);
            $validatedData['image'] = $this->uploadImage($request->file('image'), 'products');
        }

        $product->fill($validatedData);
        $product->save();

        if ($variantsPayload) {
            // Sync variants: easiest way is to delete old and recreate new
            // A more complex approach would be to update existing ones by ID
            $product->load('variants.images');
            foreach ($product->variants as $oldVariant) {
                foreach ($oldVariant->images as $image) {
                    $this->deleteImageIfLocal($image->path);
                }
            }
            $product->variants()->delete();

            $variants = json_decode($variantsPayload, true);
            if (is_array($variants)) {
                $this->validateVariantsPayload($variants);
                $this->syncVariantsWithImages($request, $product, $variants);
            } else {
                throw ValidationException::withMessages([
                    'variants' => ['Variants payload is invalid.'],
                ]);
            }
        }

        return response()->json($product->load('category:id,name', 'variants.color', 'variants.size', 'variants.images'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->load('variants.images');
        foreach ($product->variants as $variant) {
            foreach ($variant->images as $image) {
                $this->deleteImageIfLocal($image->path);
            }
        }

        $this->deleteImageIfLocal($product->image);

        DB::transaction(function () use ($product) {
            foreach ($product->variants as $variant) {
                $variant->images()->delete();
            }
            $product->variants()->delete();
            $product->delete();
        });

        return response()->json(null, 204);
    }

    private function syncVariantsWithImages(Request $request, Product $product, array $variants): void
    {
        foreach ($variants as $index => $variantData) {
            $variant = $product->variants()->create([
                'color_id' => $variantData['color_id'] ?? null,
                'size_id' => $variantData['size_id'] ?? null,
                'stock' => $variantData['stock'] ?? 0,
                'price' => $variantData['price'],
            ]);

            $existingImagePaths = $variantData['existing_image_paths'] ?? [];
            foreach ($existingImagePaths as $position => $path) {
                if ($path) {
                    $variant->images()->create([
                        'path' => $path,
                        'sort_order' => $position,
                    ]);
                }
            }

            $uploadedFiles = $request->file("variant_images.$index", []);
            foreach ($uploadedFiles as $position => $imageFile) {
                $pathOrUrl = $this->uploadImage($imageFile, 'product-variants');
                $variant->images()->create([
                    'path' => $pathOrUrl,
                    'sort_order' => count($existingImagePaths) + $position,
                ]);
            }
        }
    }

    private function uploadImage(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        if (env('CLOUDINARY_URL')) {
            $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'e-store/' . $folder,
            ]);

            return $result['secure_url'] ?? $result['url'];
        }

        return $file->store($folder, 'public');
    }

    private function deleteImageIfLocal(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }

    private function validateVariantsPayload(array $variants): void
    {
        if (count($variants) === 0) {
            throw ValidationException::withMessages([
                'variants' => ['At least one variant is required.'],
            ]);
        }

        foreach ($variants as $index => $variant) {
            $price = $variant['price'] ?? null;
            if (!is_numeric($price) || (float) $price <= 0) {
                throw ValidationException::withMessages([
                    "variants.$index.price" => ['Each variant must have a valid price greater than 0.'],
                ]);
            }
        }
    }
}
