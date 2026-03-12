<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Review;
use App\Models\WishlistItem;

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
}
