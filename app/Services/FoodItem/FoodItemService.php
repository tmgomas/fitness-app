<?php

namespace App\Services\FoodItem;

use App\Repositories\FoodItem\Interfaces\FoodItemRepositoryInterface;
use App\Services\FoodItem\Interfaces\FoodItemServiceInterface;

class FoodItemService implements FoodItemServiceInterface
{
    public function __construct(
        private readonly FoodItemRepositoryInterface $foodItemRepository
    ) {}

    public function getAllFoodItems(array $filters = [])
    {
        $query = $this->foodItemRepository->query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        // with() method එක එකතු කිරීම
        $query->with('foodNutrition.nutritionType');

        return $query->latest()->paginate(10);
    }

    public function createFoodItem(array $data)
    {
        $foodData = $this->extractFoodData($data);
        $nutritionData = $data['nutrition'] ?? [];

        return $this->foodItemRepository->createWithNutrition($foodData, $nutritionData);
    }

    public function updateFoodItem(string $id, array $data)
    {
        $foodData = $this->extractFoodData($data);
        $nutritionData = $data['nutrition'] ?? [];

        return $this->foodItemRepository->updateWithNutrition($id, $foodData, $nutritionData);
    }

    public function deleteFoodItem(string $id)
    {
        return $this->foodItemRepository->delete($id);
    }

    public function searchFoodItems(string $query)
    {
        $foodItems = $this->foodItemRepository->searchActive($query);

        // Eager loading the relationship
        $foodItems->load('foodNutrition.nutritionType');

        return $foodItems;
    }
    public function getFoodItemById(string $id)
    {
        return $this->foodItemRepository->find($id)->load('foodNutrition.nutritionType');
    }

    private function extractFoodData(array $data): array
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'],
            'serving_size' => $data['serving_size'],
            'serving_unit' => $data['serving_unit'],
            'weight_per_serving' => $data['weight_per_serving'],
            'image_url' => $data['image_url'] ?? null,
            'is_active' => $data['is_active'] ?? true
        ];
    }
}
