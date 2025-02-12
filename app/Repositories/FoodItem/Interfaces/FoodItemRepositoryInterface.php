<?php

namespace App\Repositories\FoodItem\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface FoodItemRepositoryInterface extends BaseRepositoryInterface
{
    public function searchActive(string $query, int $perPage = 10);
    public function createWithNutrition(array $foodData, array $nutritionData);
    public function updateWithNutrition(string $id, array $foodData, array $nutritionData);
}
