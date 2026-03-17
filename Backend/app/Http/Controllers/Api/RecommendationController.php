<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    /**
     * "Users like you also liked" - collaborative filtering.
     * Finds similar users (same products bought) and recommends products they liked.
     */
    public function usersLikeYouAlsoLiked(Request $request)
    {
        $limit = min((int) $request->get('limit', 4), 20);
        $user = $request->user();

        if (!$user) {
            return $this->fallbackRecommendations($limit);
        }

        $userProductIds = OrderItem::whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->pluck('product_id')
            ->unique()
            ->values();

        if ($userProductIds->isEmpty()) {
            return $this->fallbackRecommendations($limit);
        }

        $similarUserIds = OrderItem::whereIn('product_id', $userProductIds)
            ->whereHas('order', fn ($q) => $q->where('user_id', '!=', $user->id))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->pluck('orders.user_id')
            ->unique()
            ->values();

        if ($similarUserIds->isEmpty()) {
            return $this->fallbackRecommendations($limit);
        }

        $recommendedProductIds = OrderItem::whereHas('order', fn ($q) => $q->whereIn('user_id', $similarUserIds))
            ->whereNotIn('product_id', $userProductIds)
            ->select('product_id', DB::raw('COUNT(*) as score'))
            ->groupBy('product_id')
            ->orderByDesc('score')
            ->limit($limit * 2)
            ->pluck('product_id');

        if ($recommendedProductIds->isEmpty()) {
            return $this->fallbackRecommendations($limit);
        }

        $ids = $recommendedProductIds->take($limit)->values();
        $products = Product::with('category:id,name', 'variants.color', 'variants.size', 'variants.images')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . $ids->implode(',') . ')')
            ->get();

        return response()->json($products);
    }

    protected function fallbackRecommendations(int $limit)
    {
        return response()->json(
            Product::with('category:id,name', 'variants.color', 'variants.size', 'variants.images')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->inRandomOrder()
                ->limit($limit)
                ->get()
        );
    }
}
