<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product:id,name,image'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping' => 'required|array',
            'shipping.fullName' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.phoneNumber' => 'required|string',
            'shipping.streetAddress' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.state' => 'required|string',
            'shipping.zipCode' => 'required|string',
            'shipping.country' => 'required|string',
            
            'payment' => 'required|array',
            'payment.method' => 'required|string|in:card,stripe,paypal,cod',
            
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|exists:products,id',
            'items.*.variantId' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.name' => 'required|string',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            
            'totals' => 'required|array',
            'totals.subtotal' => 'required|numeric',
            'totals.shipping' => 'required|numeric',
            'totals.tax' => 'required|numeric',
            'totals.total' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = $request->user();
            
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'full_name' => $validated['shipping']['fullName'],
                'email' => $validated['shipping']['email'],
                'phone_number' => $validated['shipping']['phoneNumber'],
                'street_address' => $validated['shipping']['streetAddress'],
                'city' => $validated['shipping']['city'],
                'state' => $validated['shipping']['state'],
                'zip_code' => $validated['shipping']['zipCode'],
                'country' => $validated['shipping']['country'],
                'payment_method' => $validated['payment']['method'],
                'payment_status' => $validated['payment']['method'] === 'cod' ? 'pending' : 'paid', // For now, mock payment as paid if not COD
                'status' => 'pending',
                'subtotal' => $validated['totals']['subtotal'],
                'shipping' => $validated['totals']['shipping'],
                'tax' => $validated['totals']['tax'],
                'total' => $validated['totals']['total'],
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['productId'],
                    'product_variant_id' => $item['variantId'],
                    'product_name' => $item['name'],
                    'variant_info' => ($item['color'] ?? '-') . ' / ' . ($item['size'] ?? '-'),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Clear the user's cart after successful order
            $user->cartItems()->delete();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('items')
            ], 201);
        });
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        return response()->json($order->load(['items.product:id,name,image']));
    }
}
