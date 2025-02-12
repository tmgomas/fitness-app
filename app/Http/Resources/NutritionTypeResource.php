<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NutritionTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'nutrition_id' => $this->nutrition_id,
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
            'food_nutrition_count' => $this->foodNutrition()->count(),
            'meal_nutrition_count' => $this->mealNutrition()->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
