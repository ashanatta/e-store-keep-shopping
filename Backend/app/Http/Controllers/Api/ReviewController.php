<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'summary' => [
                'count' => $reviews->count(),
                'average' => round((float) $reviews->avg('rating'), 1),
            ],
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json(
            $review->load('user:id,name'),
            201
        );
    }

    public function destroy(Request $request, Review $review)
    {
        if ($request->user()->id !== $review->user_id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return response()->json(null, 204);
    }
}
