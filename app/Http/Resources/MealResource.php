<?php
// app/Http/Resources/MealResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'meal_id' => $this->meal_id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'default_serving_size' => $this->default_serving_size,
            'serving_unit' => $this->serving_unit,
            'is_active' => $this->is_active,
            'nutrition_facts' => MealNutritionResource::collection($this->whenLoaded('nutritionFacts')),
            'foods' => MealFoodResource::collection($this->whenLoaded('foods')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
