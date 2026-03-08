<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
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

        try {
            return DB::transaction(function () use ($request, $validated) {
                $user = $request->user();
                
                // First, check and deduct stock for each item
                $itemsToProcess = [];
                foreach ($validated['items'] as $itemData) {
                    if (isset($itemData['variantId'])) {
                        $variant = ProductVariant::where('id', $itemData['variantId'])->lockForUpdate()->first();
                        
                        if (!$variant) {
                            throw new \Exception("Product variant not found.");
                        }

                        if ($variant->stock < $itemData['quantity']) {
                            throw new \Exception("Insufficient stock for product: {$itemData['name']}. Only {$variant->stock} left.");
                        }

                        // Deduct stock
                        $variant->stock -= $itemData['quantity'];
                        $variant->save();
                        
                        $itemsToProcess[] = [
                            'data' => $itemData,
                            'variant' => $variant
                        ];
                    } else {
                        // If your system supports products without variants, handle that here.
                        // For now, based on your current setup, we'll assume variants are used.
                        throw new \Exception("Product variant is required for order.");
                    }
                }

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
                    'payment_status' => $validated['payment']['method'] === 'cod' ? 'pending' : 'paid',
                    'status' => 'pending',
                    'subtotal' => $validated['totals']['subtotal'],
                    'shipping' => $validated['totals']['shipping'],
                    'tax' => $validated['totals']['tax'],
                    'total' => $validated['totals']['total'],
                ]);

                foreach ($itemsToProcess as $processedItem) {
                    $item = $processedItem['data'];
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
            }, 5); // Retry 5 times in case of deadlock
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
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
