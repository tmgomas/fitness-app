<?php

namespace App\Services\FoodNutrition;

use App\Repositories\FoodNutrition\Interfaces\FoodNutritionRepositoryInterface;
use App\Services\FoodNutrition\Interfaces\FoodNutritionServiceInterface;

class FoodNutritionService implements FoodNutritionServiceInterface
{
    public function __construct(
        private readonly FoodNutritionRepositoryInterface $foodNutritionRepository
    ) {}

    public function getAllFoodNutritions(array $filters = [])
    {
        if (!empty($filters['search'])) {
            return $this->foodNutritionRepository->searchWithRelations($filters['search']);
        }
        
        return $this->foodNutritionRepository->getAllWithRelations();
    }

    public function createFoodNutrition(array $data)
    {
        return $this->foodNutritionRepository->create($data);
    }

    public function updateFoodNutrition(string $id, array $data)
    {
        return $this->foodNutritionRepository->update($id, $data);
    }

    public function deleteFoodNutrition(string $id)
    {
        return $this->foodNutritionRepository->delete($id);
    }

    public function getFoodNutritionById(string $id)
    {
        return $this->foodNutritionRepository->find($id);
    }

    public function searchFoodNutritions(string $query)
    {
        return $this->foodNutritionRepository->searchWithRelations($query);
    }
}