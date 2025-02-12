<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodNutritionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->food_nutrition_id,
            'nutrition_id' => $this->nutrition_id,
            'amount_per_100g' => $this->amount_per_100g,
            'measurement_unit' => $this->measurement_unit,
            'nutrition_type' => new NutritionTypeResource($this->whenLoaded('nutritionType')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
