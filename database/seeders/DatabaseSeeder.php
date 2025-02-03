<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\NutritionType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      

        // Call NutritionTypeSeeder
        $this->call([
            NutritionTypeSeeder::class,
            UserSeeder::class
        ]);
    }
}