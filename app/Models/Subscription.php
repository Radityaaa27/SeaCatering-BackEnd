<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'meal_plan_id',
        'meal_types',
        'delivery_days',
        'allergies',
        'total_price',
        'status'
    ];

    protected $casts = [
        'meal_types' => 'array',
        'delivery_days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mealPlan()
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}