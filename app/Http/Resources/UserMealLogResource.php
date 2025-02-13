<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMealLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->meal_log_id,
            'user_id' => $this->user_id,
            'meal_id' => $this->meal_id,
            'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
            'meal_type' => $this->meal_type,
            'serving_size' => (float) $this->serving_size,
            'serving_unit' => $this->serving_unit,

            // Include meal details when loaded
            'meal' => $this->when($this->relationLoaded('meal'), function () {
                return [
                    'id' => $this->meal->meal_id,
                    'name' => $this->meal->name,
                    'description' => $this->meal->description,
                    'image_url' => $this->meal->image_url,
                    'default_serving_size' => (float) $this->meal->default_serving_size,
                    'serving_unit' => $this->meal->serving_unit,

                    // Include nutrition facts when loaded
                    'nutrition_facts' => $this->when(
                        $this->meal->relationLoaded('nutritionFacts'),
                        function () {
                            return $this->meal->nutritionFacts->map(function ($nutritionFact) {
                                return [
                                    'id' => $nutritionFact->meal_nutrition_id,
                                    'amount_per_100g' => (float) $nutritionFact->amount_per_100g,
                                    'measurement_unit' => $nutritionFact->measurement_unit,
                                    'nutrition_type' => [
                                        'id' => $nutritionFact->nutritionType->nutrition_id,
                                        'name' => $nutritionFact->nutritionType->name,
                                        'unit' => $nutritionFact->nutritionType->unit
                                    ]
                                ];
                            });
                        }
                    ),

                    // Include meal foods when loaded
                    'foods' => $this->when(
                        $this->meal->relationLoaded('foods'),
                        function () {
                            return $this->meal->foods->map(function ($food) {
                                return [
                                    'id' => $food->meal_food_id,
                                    'quantity' => (float) $food->quantity,
                                    'unit' => $food->unit,
                                    'food_item' => [
                                        'id' => $food->foodItem->food_id,
                                        'name' => $food->foodItem->name,
                                        'description' => $food->foodItem->description,
                                        'serving_size' => (float) $food->foodItem->serving_size,
                                        'serving_unit' => $food->foodItem->serving_unit,

                                        // Include food nutrition when loaded
                                        'nutrition' => $this->when(
                                            $food->foodItem->relationLoaded('foodNutrition'),
                                            function () use ($food) {
                                                return $food->foodItem->foodNutrition->map(function ($nutrition) {
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
                                    ]
                                ];
                            });
                        }
                    )
                ];
            }),

            // Calculate total nutrition based on serving size
            'calculated_nutrition' => $this->when(
                $this->relationLoaded('meal') && $this->meal->relationLoaded('nutritionFacts'),
                function () {
                    $servingMultiplier = $this->serving_size / 100;
                    return $this->meal->nutritionFacts->map(function ($nutritionFact) use ($servingMultiplier) {
                        return [
                            'nutrition_type' => [
                                'id' => $nutritionFact->nutritionType->nutrition_id,
                                'name' => $nutritionFact->nutritionType->name,
                                'unit' => $nutritionFact->nutritionType->unit
                            ],
                            'amount' => (float) ($nutritionFact->amount_per_100g * $servingMultiplier),
                            'measurement_unit' => $nutritionFact->measurement_unit
                        ];
                    });
                }
            ),

            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
