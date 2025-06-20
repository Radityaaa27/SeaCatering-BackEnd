<?php
namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\MealPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller {
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_plan_id' => 'required|exists:meal_plans,id',
            'meal_types' => 'required|array|min:1',
            'delivery_days' => 'required|array|min:1',
            'allergies' => 'nullable|string',
            'user_id' => 'nullable|integer' // Tambahkan ini jika masih butuh user_id
        ]);
    
        try {
            $mealPlan = MealPlan::findOrFail($validated['meal_plan_id']);
            
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'] ?? null, 
                'meal_plan_id' => $mealPlan->id,
                'meal_types' => $validated['meal_types'],
                'delivery_days' => $validated['delivery_days'],
                'allergies' => $validated['allergies'],
                'total_price' => $this->calculatePrice($validated)
            ]);
    
            return response()->json([
                'message' => 'Subscription created successfully',
                'data' => $subscription->load('mealPlan')
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculatePrice($data) {
        $mealPlan = MealPlan::find($data['meal_plan_id']);
        $mealCount = count($data['meal_types']);
        $dayCount = count($data['delivery_days']);
        return $mealPlan->price * $mealCount * $dayCount * 4.3;
    }
}