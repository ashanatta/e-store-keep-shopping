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
    public function orders()
    {
        return response()->json(
            Order::with(['user:id,name,email', 'items'])
                ->latest()
                ->get()
        );
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed',
        ]);

        $order->update($validated);

        return response()->json($order);
    }

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
}
