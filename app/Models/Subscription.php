<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 
        'meal_plan_id', 
        'meal_types',
        'delivery_days',
        'allergies',
        'total_price'
    ];
    
    protected $casts = [
        'meal_types' => 'array',
        'delivery_days' => 'array'
    ];
    
    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}