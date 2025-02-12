<?php

namespace App\Repositories\FoodNutrition\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface FoodNutritionRepositoryInterface extends BaseRepositoryInterface
{
    public function searchWithRelations(string $searchTerm, int $perPage = 10);
    public function getAllWithRelations(int $perPage = 10);
}
