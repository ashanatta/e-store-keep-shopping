<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Review;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class AdminCommerceController extends Controller
{
    public function reviews()
    {
        return response()->json(
            Review::with('user:id,name,email', 'product:id,name')
                ->latest()
                ->get()
        );
    }

    public function wishlists()
    {
        return response()->json(
            WishlistItem::with('user:id,name,email', 'product:id,name')
                ->latest()
                ->get()
        );
    }

    public function carts()
    {
        return response()->json(
            CartItem::with([
                'user:id,name,email',
                'product:id,name',
                'variant:id,product_id,color_id,size_id,price',
                'variant.color:id,name',
                'variant.size:id,name',
            ])->latest()->get()
        );
    }

    public function orders()
    {
        return response()->json(
            Order::with(['user:id,name,email', 'items.product:id,name,image'])
                ->latest()
                ->get()
        );
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json($order->load(['user:id,name,email', 'items.product:id,name,image']));
    }

    public function stats()
    {
        return response()->json([
            'orders_count' => Order::count(),
            'reviews_count' => Review::count(),
            'wishlists_count' => WishlistItem::count(),
            'carts_count' => CartItem::count(),
        ]);
    }
}
