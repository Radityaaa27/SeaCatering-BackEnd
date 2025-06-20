<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    protected $fillable = [
        'name', 'price', 'shortDescription', 
        'longDescription', 'benefits', 'image'
    ];
    
    protected $casts = [
        'benefits' => 'array'
    ];
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

}