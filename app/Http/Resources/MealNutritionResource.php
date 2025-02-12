<?php
// app/Http/Resources/MealNutritionResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealNutritionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'nutrition_id' => $this->nutrition_id,
            'amount_per_100g' => $this->amount_per_100g,
            'measurement_unit' => $this->measurement_unit,
            'nutrition_type' => $this->nutritionType ? [
                'nutrition_id' => $this->nutritionType->nutrition_id,
                'name' => $this->nutritionType->name,
                'unit' => $this->nutritionType->unit
            ] : null
        ];
    }
}
