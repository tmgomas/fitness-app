<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $foodItems = [
            [
                'name' => 'සම්බල්',
                'description' => 'පොල් සම්බල්',
                'serving_size' => 50.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'දාල් කරි',
                'description' => 'පරිප්පු කරි',
                'serving_size' => 100.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'බත්',
                'description' => 'සුදු බත්',
                'serving_size' => 100.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'මාළු කරි',
                'description' => 'තෝර මාළු කරි',
                'serving_size' => 100.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'පොල් සම්බෝල',
                'description' => 'පොල් සම්බෝල',
                'serving_size' => 50.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'ගෝවා මාලුව',
                'description' => 'ගෝවා තෙම්පරාදුව',
                'serving_size' => 75.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ]
        ];

        foreach ($foodItems as $item) {
            FoodItem::create($item);
        }
    }
}
