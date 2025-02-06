<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use App\Models\NutritionType;
use App\Models\FoodNutrition;
use Illuminate\Database\Seeder;

class FoodNutritionSeeder extends Seeder
{
    public function run(): void
    {
        // බත් සඳහා පෝෂණ තොරතුරු add කරමු
        $rice = FoodItem::where('name', 'බත්')->first();
        $nutritionValues = [
            'Calories' => ['amount' => 130, 'unit' => 'kcal'],
            'Carbohydrates' => ['amount' => 28, 'unit' => 'g'],
            'Protein' => ['amount' => 2.7, 'unit' => 'g'],
            'Fat' => ['amount' => 0.3, 'unit' => 'g'],
            'Fiber' => ['amount' => 0.4, 'unit' => 'g']
        ];

        foreach ($nutritionValues as $nutritionName => $values) {
            $nutritionType = NutritionType::where('name', $nutritionName)->first();
            if ($nutritionType && $rice) {
                FoodNutrition::create([
                    'food_id' => $rice->food_id,
                    'nutrition_id' => $nutritionType->nutrition_id,
                    'amount_per_100g' => $values['amount'],
                    'measurement_unit' => $values['unit']
                ]);
            }
        }

        // දාල් කරි සඳහා පෝෂණ තොරතුරු
        $dhal = FoodItem::where('name', 'දාල් කරි')->first();
        $dhalNutrition = [
            'Calories' => ['amount' => 116, 'unit' => 'kcal'],
            'Protein' => ['amount' => 9, 'unit' => 'g'],
            'Carbohydrates' => ['amount' => 20, 'unit' => 'g'],
            'Fat' => ['amount' => 0.4, 'unit' => 'g'],
            'Fiber' => ['amount' => 8, 'unit' => 'g']
        ];

        foreach ($dhalNutrition as $nutritionName => $values) {
            $nutritionType = NutritionType::where('name', $nutritionName)->first();
            if ($nutritionType && $dhal) {
                FoodNutrition::create([
                    'food_id' => $dhal->food_id,
                    'nutrition_id' => $nutritionType->nutrition_id,
                    'amount_per_100g' => $values['amount'],
                    'measurement_unit' => $values['unit']
                ]);
            }
        }
    }
}
