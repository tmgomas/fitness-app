<?php

namespace App\Services\FoodItem\Interfaces;

interface FoodItemServiceInterface
{
    public function getAllFoodItems(array $filters = []);
    public function createFoodItem(array $data);
    public function updateFoodItem(string $id, array $data);
    public function deleteFoodItem(string $id);
    public function searchFoodItems(string $query);
    public function getFoodItemById(string $id);
}
