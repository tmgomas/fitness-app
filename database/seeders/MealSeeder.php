<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    public function run(): void
    {
        $meals = [
            [
                'name' => 'බත් සහ කරි',
                'description' => 'බත්, දාල් කරි, මාළු කරි සහ පරිවාර',
                'default_serving_size' => 350.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'කිරිබත්',
                'description' => 'කිරිබත් සහ ලුණු මිරිස්',
                'default_serving_size' => 200.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ],
            [
                'name' => 'ඉදියාප්ප',
                'description' => 'ඉදියාප්ප සහ සම්බෝල',
                'default_serving_size' => 150.00,
                'serving_unit' => 'g',
                'image_url' => null,
                'is_active' => true
            ]
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }
}
