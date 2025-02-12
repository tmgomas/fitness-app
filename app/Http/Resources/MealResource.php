<?php
// app/Http/Resources/MealResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{ // app/Http/Resources/MealResource.php
    public function toArray(Request $request): array
    {
        // Add null check
        if (!$this->resource) {
            return [];
        }

        return [
            'meal_id' => $this->meal_id ?? null,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'image_url' => $this->image_url ?? null,
            'default_serving_size' => $this->default_serving_size ?? null,
            'serving_unit' => $this->serving_unit ?? null,
            'is_active' => $this->is_active ?? true,
            'nutrition_facts' => MealNutritionResource::collection($this->whenLoaded('nutritionFacts')),
            'foods' => MealFoodResource::collection($this->whenLoaded('foods')),
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null
        ];
    }
}
