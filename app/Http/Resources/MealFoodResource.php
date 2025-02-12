<?php
// app/Http/Resources/MealFoodResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealFoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'food_id' => $this->food_id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'food_item' => $this->foodItem ? [
                'food_id' => $this->foodItem->food_id,
                'name' => $this->foodItem->name,
                'description' => $this->foodItem->description
            ] : null
        ];
    }
}
