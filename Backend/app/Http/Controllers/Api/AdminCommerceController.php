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

    public function stats()
    {
        $totalUsers    = User::count();
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalSales    = Order::where('payment_status', 'paid')->sum('total');

        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Revenue over last 7 days
        $revenueByDay = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $last7Days[] = [
                'date'    => $date,
                'revenue' => isset($revenueByDay[$date]) ? (float) $revenueByDay[$date]->revenue : 0,
            ];
        }

        // Orders over last 7 days
        $ordersByDay = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $last7DaysOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $last7DaysOrders[] = [
                'date'  => $date,
                'count' => isset($ordersByDay[$date]) ? (int) $ordersByDay[$date]->count : 0,
            ];
        }

        return response()->json([
            'total_users'       => $totalUsers,
            'total_products'    => $totalProducts,
            'total_orders'      => $totalOrders,
            'total_sales'       => $totalSales,
            'orders_by_status'  => $ordersByStatus,
            'revenue_by_day'    => $last7Days,
            'orders_by_day'     => $last7DaysOrders,
        ]);
    }
}
