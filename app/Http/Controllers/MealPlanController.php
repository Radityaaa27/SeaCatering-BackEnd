<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MealPlan;

class MealPlanController extends Controller
{
    public function index()
    {
        return MealPlan::all();
    }
    public function mealPlans()
    {
        $mealPlans = MealPlan::all();
        return response()->json([
            'message' => 'meal plans retrieved successfully',
            'data' => $mealPlans
        ]);
    }
}
