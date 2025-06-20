<?php

use App\Http\Controllers\MealPlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show']);
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);
    Route::post('/subscriptions/calculate-price', [SubscriptionController::class, 'calculate']);

Route::get('/meal-plans', [MealPlanController::class, 'mealPlans']);