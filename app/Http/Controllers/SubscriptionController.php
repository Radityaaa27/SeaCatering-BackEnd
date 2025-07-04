<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\MealPlan;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal_plan_id' => 'required|integer',
            'meal_types' => 'required|array|min:1',
            'meal_types.*' => 'string|in:Breakfast,Lunch,Dinner',
            'delivery_days' => 'required|array|min:1',
            'delivery_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'allergies' => 'nullable|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $mealPlan = MealPlan::findOrFail($request->meal_plan_id);
            
            $subscription = $request->user()->subscriptions()->create([
                'meal_plan_id' => $mealPlan->id,
                'meal_types' => $request->meal_types,
                'delivery_days' => $request->delivery_days,
                'allergies' => $request->allergies,
                'name' => $request->name,
                'phone' => $request->phone,
                'total_price' => $this->calculatePrice($request->all())
            ]);

            // Automatically create the first order
            $order = $this->createInitialOrder($subscription);

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => $subscription->load('mealPlan'),
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculatePrice($data)
    {
        $mealPlan = MealPlan::find($data['meal_plan_id']);
        $mealCount = count($data['meal_types']);
        $dayCount = count($data['delivery_days']);
        return $mealPlan->price * $mealCount * $dayCount * 4.3; // 4.3 weeks in a month
    }

    private function createInitialOrder(Subscription $subscription)
    {
        $mealPlan = $subscription->mealPlan;
        
        return Order::create([
            'user_id' => $subscription->user_id,
            'name' => $subscription->name,               // From subscription
            'phone' => $subscription->phone,             // From subscription
            'plan_name' => $mealPlan->name,              // From meal plan
            'total_price' => $subscription->total_price,
            'meal_types' => json_encode($subscription->meal_types), 
            'delivery_days' => json_encode($subscription->delivery_days),
            'allergies' => $subscription->allergies,     // From subscription
            'order_date' => now(),
            'status' => 'pending'
    ]);
}

    private function calculateFirstDeliveryDate(array $deliveryDays)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $today = now();
        
        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            $dayName = $days[$date->dayOfWeek];
            
            if (in_array($dayName, $deliveryDays)) {
                return $date->toDateString();
            }
        }
        
        return $today->addDays(1)->toDateString();
    }

    public function showWithOrders($id)
    {
        $subscription = Subscription::with(['orders', 'mealPlan'])->findOrFail($id);
        return response()->json($subscription);
    }
}