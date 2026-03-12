<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        try {
            $totalUsers = User::count();
            $totalOrders = Order::count();
            $totalProducts = Product::count();
            $totalSales = Order::whereIn('status', ['delivered', 'shipped', 'processing'])->sum('total');

            $revenueByDay = Order::whereIn('status', ['delivered', 'shipped', 'processing'])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($r) => ['date' => (string) $r->date, 'revenue' => (float) $r->revenue])
                ->values()
                ->all();

            $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $ordersByDay = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($r) => ['date' => (string) $r->date, 'count' => (int) $r->count])
                ->values()
                ->all();

            return response()->json([
                'total_users' => $totalUsers,
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_sales' => (float) $totalSales,
                'revenue_by_day' => $revenueByDay,
                'orders_by_status' => $ordersByStatus,
                'orders_by_day' => $ordersByDay,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin stats error: ' . $e->getMessage());
            return response()->json([
                'total_users' => 0,
                'total_orders' => 0,
                'total_products' => 0,
                'total_sales' => 0,
                'revenue_by_day' => [],
                'orders_by_status' => [],
                'orders_by_day' => [],
            ]);
        }
    }
}
