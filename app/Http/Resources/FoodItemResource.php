<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->food_id,
            'name' => $this->name,
            'description' => $this->description,
            'serving_size' => $this->serving_size,
            'serving_unit' => $this->serving_unit,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'nutrition' => FoodNutritionResource::collection($this->whenLoaded('foodNutrition')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
