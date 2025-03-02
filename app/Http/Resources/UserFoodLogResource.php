<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFoodLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->food_log_id,
            'user_id' => $this->user_id,
            'food_id' => $this->food_id,
            'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
            'meal_type' => $this->meal_type,
            'serving_size' => (float) $this->serving_size,
            'serving_unit' => $this->serving_unit,

            'food_item' => $this->when($this->relationLoaded('foodItem'), function () {
                return [
                    'id' => $this->foodItem->food_id,
                    'name' => $this->foodItem->name,
                    'description' => $this->foodItem->description,
                    'serving_size' => (float) $this->foodItem->serving_size,
                    'serving_unit' => $this->foodItem->serving_unit,

                    'nutrition' => $this->when(
                        $this->foodItem->relationLoaded('foodNutrition'),
                        function () {
                            return $this->foodItem->foodNutrition->map(function ($nutrition) {
                                return [
                                    'id' => $nutrition->food_nutrition_id,
                                    'amount_per_100g' => (float) $nutrition->amount_per_100g,
                                    'measurement_unit' => $nutrition->measurement_unit,
                                    'nutrition_type' => [
                                        'id' => $nutrition->nutritionType->nutrition_id,
                                        'name' => $nutrition->nutritionType->name,
                                        'unit' => $nutrition->nutritionType->unit
                                    ]
                                ];
                            });
                        }
                    )
                ];
            }),

            'calculated_nutrition' => $this->when(
                $this->relationLoaded('foodItem') && $this->foodItem->relationLoaded('foodNutrition'),
                function () {
                    // සේවින් එකකට ග්‍රෑම් ගණන, නැත්නම් default 100g
                    $weightPerServing = $this->foodItem->weight_per_serving ?? 100;

                    // මුළු ග්‍රෑම් ප්‍රමාණය
                    $servingWeightInGrams = $this->serving_size * $weightPerServing;

                    // පෝෂක ගණනය කිරීම
                    return $this->foodItem->foodNutrition->map(function ($nutrition) use ($servingWeightInGrams) {
                        return [
                            'nutrition_type' => [
                                'id' => $nutrition->nutritionType->nutrition_id,
                                'name' => $nutrition->nutritionType->name,
                                'unit' => $nutrition->nutritionType->unit
                            ],
                            'amount' => (float) ($nutrition->amount_per_100g * $servingWeightInGrams / 100),
                            'measurement_unit' => $nutrition->measurement_unit
                        ];
                    });
                }
            ),

            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
