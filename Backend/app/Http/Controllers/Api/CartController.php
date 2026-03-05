<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()
            ->cartItems()
            ->with([
                'product:id,name,image,category_id',
                'product.category:id,name',
                'variant:id,product_id,color_id,size_id,price',
                'variant.color:id,name',
                'variant.size:id,name',
            ])
            ->latest()
            ->get();

        return response()->json([
            'items' => $items,
            'count' => (int) $items->sum('quantity'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $validated['quantity'] ?? 1;

        $item = CartItem::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['product_variant_id'] ?? null,
            ],
            [
                'quantity' => 0,
            ]
        );

        $item->quantity += $quantity;
        $item->save();

        return response()->json(
            $item->load([
                'product:id,name,image,category_id',
                'product.category:id,name',
                'variant:id,product_id,color_id,size_id,price',
                'variant.color:id,name',
                'variant.size:id,name',
            ]),
            201
        );
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($request->user()->id !== $cartItem->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json($cartItem);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($request->user()->id !== $cartItem->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $cartItem->delete();

        return response()->json(null, 204);
    }

    public function clear(Request $request)
    {
        $request->user()->cartItems()->delete();

        return response()->json(null, 204);
    }
}
