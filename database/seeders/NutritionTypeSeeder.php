<?php

namespace Database\Seeders;

use App\Models\NutritionType;
use Illuminate\Database\Seeder;

class NutritionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $nutritionTypes = [
            // Macronutrients
            [
                'name' => 'Calories',
                'description' => 'Energy content of food',
                'unit' => 'kcal',
                'is_active' => true
            ],
            [
                'name' => 'Protein',
                'description' => 'Essential for building and repairing tissues',
                'unit' => 'g',
                'is_active' => true
            ],
            [
                'name' => 'Carbohydrates',
                'description' => 'Main source of energy for the body',
                'unit' => 'g',
                'is_active' => true
            ],
            [
                'name' => 'Fat',
                'description' => 'Important for nutrient absorption and hormone production',
                'unit' => 'g',
                'is_active' => true
            ],
            [
                'name' => 'Fiber',
                'description' => 'Aids digestion and promotes gut health',
                'unit' => 'g',
                'is_active' => true
            ],

            // Vitamins
            [
                'name' => 'Vitamin A',
                'description' => 'Important for vision and immune system',
                'unit' => 'IU',
                'is_active' => true
            ],
            [
                'name' => 'Vitamin C',
                'description' => 'Antioxidant that supports immune system',
                'unit' => 'mg',
                'is_active' => true
            ],
            [
                'name' => 'Vitamin D',
                'description' => 'Essential for bone health and immune function',
                'unit' => 'mcg',
                'is_active' => true
            ],

            // Minerals
            [
                'name' => 'Calcium',
                'description' => 'Essential for bone and teeth health',
                'unit' => 'mg',
                'is_active' => true
            ],
            [
                'name' => 'Iron',
                'description' => 'Important for blood oxygen transport',
                'unit' => 'mg',
                'is_active' => true
            ],
            [
                'name' => 'Potassium',
                'description' => 'Helps regulate fluid balance and nerve signals',
                'unit' => 'mg',
                'is_active' => true
            ],

            // Others
            [
                'name' => 'Cholesterol',
                'description' => 'Fat-like substance used to build cells',
                'unit' => 'mg',
                'is_active' => true
            ],
            [
                'name' => 'Sugar',
                'description' => 'Simple carbohydrates',
                'unit' => 'g',
                'is_active' => true
            ],
            [
                'name' => 'Saturated Fat',
                'description' => 'Type of fat that is solid at room temperature',
                'unit' => 'g',
                'is_active' => true
            ],
        ];

        foreach ($nutritionTypes as $type) {
            NutritionType::create($type);
        }
    }
}