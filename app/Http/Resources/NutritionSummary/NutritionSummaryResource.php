<?php

namespace App\Http\Resources\NutritionSummary;

use Illuminate\Http\Resources\Json\JsonResource;

class NutritionSummaryResource extends JsonResource
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
            'date' => $this['date'],
            'user_id' => $this['user_id'],
            'calories' => [
                'consumed' => $this['calories']['consumed'],
                'burned' => $this['calories']['burned'],
                'net' => $this['calories']['net'],
                'recommended' => $this['calories']['recommended'],
                'remaining' => $this['calories']['remaining']
            ],
            'nutrition_breakdown' => $this['nutrition_breakdown'],
            'meal_types' => $this['meal_types'],
            'food_logs_count' => $this['food_logs_count'],
            'exercise_logs_count' => $this['exercise_logs_count']
        ];
    }
}
