<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Get authenticated user's orders
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['subscription' => function($query) {
                $query->with(['mealPlan']);
            }])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'total_price' => $order->total_price,
                    'subscription' => [
                        'id' => $order->subscription->id,
                        'name' => $order->subscription->name,
                        'image' => $order->subscription->mealPlan->image ?? null,
                        'price' => $order->subscription->total_price,
                        'meal_types' => $order->subscription->meal_types,
                        'delivery_days' => $order->subscription->delivery_days
                    ]
                ];
            });
            
        return response()->json($orders);
    }

    // Create new order from subscription
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,id',
            'delivery_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Get subscription details
        $subscription = Subscription::with('mealPlan')->find($request->subscription_id);
    
        $order = $request->user()->orders()->create([
            'subscription_id' => $request->subscription_id,
            'order_date' => now(),
            'delivery_date' => $request->delivery_date,
            'status' => 'pending',
            'notes' => $request->notes,
            'total_price' => $subscription->total_price
        ]);
    
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('subscription.mealPlan')
        ], 201);
    }

    // View specific order
    public function show(Request $request, Order $order)
    {
        if ($request->user()->cannot('view', $order)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load('subscription'));
    }

    // Cancel order
    public function cancel(Request $request, Order $order)
    {
        if ($request->user()->cannot('update', $order)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Order cancelled successfully']);
    }

    // ADMIN: Get all orders
    public function adminIndex()
    {
        $orders = Order::with(['user', 'subscription'])
            ->latest()
            ->get();
            
        return response()->json($orders);
    }

    // ADMIN: Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order
        ]);
    }
}