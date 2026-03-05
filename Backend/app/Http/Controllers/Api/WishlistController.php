<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()
            ->wishlistItems()
            ->with('product.category:id,name')
            ->latest()
            ->get();

        return response()->json([
            'items' => $items,
            'count' => $items->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $item = WishlistItem::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json($item, 201);
    }

    public function destroy(Request $request, Product $product)
    {
        $request->user()->wishlistItems()
            ->where('product_id', $product->id)
            ->delete();

        return response()->json(null, 204);
    }
}
