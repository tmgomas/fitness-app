<?php

namespace App\Repositories\FoodItem;

use App\Models\FoodItem;
use App\Models\FoodNutrition;
use App\Repositories\Base\BaseRepository;
use App\Repositories\FoodItem\Interfaces\FoodItemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FoodItemRepository extends BaseRepository implements FoodItemRepositoryInterface
{
    public function __construct(FoodItem $model)
    {
        parent::__construct($model);
    }

    public function searchActive(string $query, int $perPage = 10)
    {
        return $this->model
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createWithNutrition(array $foodData, array $nutritionData)
    {
        return DB::transaction(function () use ($foodData, $nutritionData) {
            $foodItem = $this->create($foodData);

            foreach ($nutritionData as $nutrition) {
                if (empty($nutrition['amount_per_100g'])) {
                    continue;
                }

                FoodNutrition::create([
                    'food_id' => $foodItem->food_id,
                    'nutrition_id' => $nutrition['nutrition_id'],
                    'amount_per_100g' => $nutrition['amount_per_100g'],
                    'measurement_unit' => $nutrition['measurement_unit']
                ]);
            }

            return $foodItem->load('foodNutrition');
        });
    }

    public function updateWithNutrition(string $id, array $foodData, array $nutritionData)
    {
        return DB::transaction(function () use ($id, $foodData, $nutritionData) {
            $foodItem = $this->update($id, $foodData);

            foreach ($nutritionData as $nutrition) {
                if (empty($nutrition['amount_per_100g'])) {
                    continue;
                }

                if (!empty($nutrition['food_nutrition_id'])) {
                    FoodNutrition::where('food_nutrition_id', $nutrition['food_nutrition_id'])
                        ->update([
                            'amount_per_100g' => $nutrition['amount_per_100g'],
                            'measurement_unit' => $nutrition['measurement_unit']
                        ]);
                } else {
                    FoodNutrition::create([
                        'food_id' => $foodItem->food_id,
                        'nutrition_id' => $nutrition['nutrition_id'],
                        'amount_per_100g' => $nutrition['amount_per_100g'],
                        'measurement_unit' => $nutrition['measurement_unit']
                    ]);
                }
            }

            return $foodItem->load('foodNutrition');
        });
    }
}
