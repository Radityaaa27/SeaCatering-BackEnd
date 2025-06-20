<?php

namespace Database\Seeders;

use App\Models\MealPlan;
use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MealPlan::create([
            'id' => 1,
            'name' => 'Diet plan',
            'price' => 30000,
            'short_Description' => 'Balanced meals for weight management',
            'long_Description' => 'Our Diet Plan offers perfectly portioned meals designed to help you achieve your weight goals without sacrificing flavor or satisfaction.',
            'benefits' => json_encode([ 
                'Calorie-controlled portions',
                'High in fiber and protein',
                'Low in refined sugars',
                'Supports sustainable weight loss'
            ]),
            'image' => 'meal-images/meal1.jpg' 
        ]);
        MealPlan::create([
            'id' =>2,
            'name' => 'Protein plan',
            'price' => 40000,
            'short_Description' => 'High-protein meals for muscle growth',
            'long_Description' => 'The Protein Plan is designed for fitness enthusiasts and anyone looking to increase their protein intake for muscle recovery and growth.',
            'benefits' => json_encode([ 
                '30-40g protein per meal',
                'Lean protein sources',
                'Supports muscle recovery',
                'Keeps you full longer'
            ]),
            'image' => 'meal-images/meal2.jpg' 
        ]);
        MealPlan::create([
            'id' => 3,
            'name' => 'Royal plan',
            'price' => 60000,
            'short_Description' => 'Premium gourmet healthy meals',
            'long_Description' => 'Our Royal Plan features chef-crafted gourmet meals made with premium ingredients for those who want healthy eating to be a luxurious experience.',
            'benefits' => json_encode([ 
                'Premium ingredients',
                'Chef-designed recipes',
                'Exotic superfoods',
                'Restaurant-quality meals'
            ]),
            'image' => 'meal-images/meal3.jpg' 
        ]);
    }
}