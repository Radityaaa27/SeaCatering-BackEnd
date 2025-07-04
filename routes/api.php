<?php

use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\OrderController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/meal-plans', [MealPlanController::class, 'mealPlans']); // Public meal plans

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'profile_picture' => $request->user()->profile_picture 
                ? asset('storage/profile_pictures/'.$request->user()->profile_picture)
                : null
        ]);
    });
    
    Route::post('/profile/update', function (Request $request) {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'nullable|string|min:8',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::delete('public/profile_pictures/'.$user->profile_picture);
                }
                
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = basename($path);
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_picture' => $user->profile_picture 
                        ? asset('storage/profile_pictures/'.$user->profile_picture)
                        : null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Subscription routes
    Route::prefix('subscriptions')->group(function () {
        Route::post('/', [SubscriptionController::class, 'store']);
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('/{subscription}', [SubscriptionController::class, 'show']);
        Route::put('/{subscription}', [SubscriptionController::class, 'update']);
        Route::delete('/{subscription}', [SubscriptionController::class, 'destroy']);
        Route::post('/calculate-price', [SubscriptionController::class, 'calculate']);
    });

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}/cancel', [OrderController::class, 'cancel']);
        
        // Admin-only routes
        Route::middleware('admin')->group(function () {
            Route::get('/all', [OrderController::class, 'adminIndex']);
            Route::put('/{order}/status', [OrderController::class, 'updateStatus']);
        });
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});