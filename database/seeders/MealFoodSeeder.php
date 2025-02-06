<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\FoodItem;
use App\Models\MealFood;
use Illuminate\Database\Seeder;

class MealFoodSeeder extends Seeder
{
    public function run(): void
    {
        // බත් සහ කරි meal එක සඳහා foods add කරමු
        $riceMeal = Meal::where('name', 'බත් සහ කරි')->first();
        $rice = FoodItem::where('name', 'බත්')->first();
        $dhal = FoodItem::where('name', 'දාල් කරි')->first();
        $fish = FoodItem::where('name', 'මාළු කරි')->first();
        $sambol = FoodItem::where('name', 'සම්බල්')->first();

        if ($riceMeal) {
            MealFood::create([
                'meal_id' => $riceMeal->meal_id,
                'food_id' => $rice->food_id,
                'quantity' => 200.00,
                'unit' => 'g'
            ]);

            MealFood::create([
                'meal_id' => $riceMeal->meal_id,
                'food_id' => $dhal->food_id,
                'quantity' => 75.00,
                'unit' => 'g'
            ]);

            MealFood::create([
                'meal_id' => $riceMeal->meal_id,
                'food_id' => $fish->food_id,
                'quantity' => 50.00,
                'unit' => 'g'
            ]);

            MealFood::create([
                'meal_id' => $riceMeal->meal_id,
                'food_id' => $sambol->food_id,
                'quantity' => 25.00,
                'unit' => 'g'
            ]);
        }
    }
}
