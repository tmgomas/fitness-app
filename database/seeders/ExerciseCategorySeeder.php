<?php

namespace Database\Seeders;

use App\Models\ExerciseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Cardio',
                'description' => 'Exercises that raise your heart rate and improve cardiovascular health.',
                'measurement_type' => 'distance'
            ],
            [
                'name' => 'Strength Training',
                'description' => 'Exercises focused on building muscle strength using weights or resistance.',
                'measurement_type' => 'weight'
            ],
            [
                'name' => 'Flexibility',
                'description' => 'Stretching exercises that improve range of motion and prevent injury.',
                'measurement_type' => 'duration'
            ],
            [
                'name' => 'HIIT',
                'description' => 'High-Intensity Interval Training that alternates between intense activity and rest.',
                'measurement_type' => 'intervals'
            ],
            [
                'name' => 'Sports',
                'description' => 'Competitive activities like basketball, soccer, tennis, etc.',
                'measurement_type' => 'duration'
            ],
            [
                'name' => 'Yoga',
                'description' => 'Mind-body practice that combines physical postures, breathing exercises, and meditation.',
                'measurement_type' => 'duration'
            ],
            [
                'name' => 'Pilates',
                'description' => 'Low-impact exercise that aims to strengthen muscles while improving flexibility and endurance.',
                'measurement_type' => 'duration'
            ],
            [
                'name' => 'Swimming',
                'description' => 'Water-based exercises that provide a full-body workout with minimal impact on joints.',
                'measurement_type' => 'distance'
            ],
            [
                'name' => 'Cycling',
                'description' => 'Pedaling activities that improve endurance and lower body strength.',
                'measurement_type' => 'distance'
            ],
            [
                'name' => 'Functional Training',
                'description' => 'Exercises that train your muscles for everyday activities.',
                'measurement_type' => 'duration'
            ],
        ];

        foreach ($categories as $category) {
            ExerciseCategory::create($category);
        }
    }
}
