<?php

namespace App\Repositories\FoodNutrition;

use App\Models\FoodNutrition;
use App\Repositories\Base\BaseRepository;
use App\Repositories\FoodNutrition\Interfaces\FoodNutritionRepositoryInterface;

class FoodNutritionRepository extends BaseRepository implements FoodNutritionRepositoryInterface
{
    public function __construct(FoodNutrition $model)
    {
        parent::__construct($model);
    }

    public function searchWithRelations(string $searchTerm, int $perPage = 10)
    {
        return $this->query()
            ->with(['food', 'nutritionType'])  // Using 'food' relationship
            ->where(function ($query) use ($searchTerm) {
                $query->whereHas('food', function ($q) use ($searchTerm) {  // Using 'food' relationship
                    $q->where('name', 'like', "%{$searchTerm}%");
                })
                    ->orWhereHas('nutritionType', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getAllWithRelations(int $perPage = 10)
    {
        return $this->query()
            ->with(['food', 'nutritionType'])  // Using 'food' relationship
            ->latest()
            ->paginate($perPage);
    }
}
