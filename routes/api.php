<?php

use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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
                // Delete old picture if exists
                if ($user->profile_picture) {
                    Storage::delete('public/profile_pictures/'.$user->profile_picture);
                }
                
                // Store new picture
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
    Route::prefix('auth')->group(function () {
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show']);
        Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);
        Route::post('/subscriptions/calculate-price', [SubscriptionController::class, 'calculate']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Public meal plans
Route::get('/meal-plans', [MealPlanController::class, 'mealPlans']);