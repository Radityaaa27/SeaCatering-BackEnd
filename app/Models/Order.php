<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'user_id',
    'name',
    'phone',
    'plan_name',
    'total_price',
    'meal_types',
    'delivery_days',
    'allergies',
    'order_date',
    'status'
];
protected $casts = [
    'meal_types' => 'array',
    'delivery_days' => 'array',
    'order_date' => 'datetime',
    'delivery_date' => 'datetime'
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class)->with('mealPlan');
    }
}