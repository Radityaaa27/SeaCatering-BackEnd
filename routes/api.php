<?php

use App\Http\Controllers\MealPlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);

    Route::prefix('auth')->middleware('auth:sanctum')->group(function (){
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show']);
        Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);
        Route::post('/subscriptions/calculate-price', [SubscriptionController::class, 'calculate']);
    });

Route::get('/meal-plans', [MealPlanController::class, 'mealPlans']);
Route::middleware('auth-sanctum')->post('/logout', [AuthController::class, 'logout']);