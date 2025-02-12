<?php

namespace App\Services\FoodNutrition\Interfaces;

interface FoodNutritionServiceInterface
{
    public function getAllFoodNutritions(array $filters = []);
    public function createFoodNutrition(array $data);
    public function updateFoodNutrition(string $id, array $data);
    public function deleteFoodNutrition(string $id);
    public function getFoodNutritionById(string $id);
    public function searchFoodNutritions(string $query);
}
