<?php

namespace App\Http\Resources\NutritionSummary;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WeeklyNutritionSummaryResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'date' => $item['date'],
                    'calories' => [
                        'consumed' => $item['calories']['consumed'],
                        'burned' => $item['calories']['burned'],
                        'net' => $item['calories']['net'],
                        'recommended' => $item['calories']['recommended'],
                        'remaining' => $item['calories']['remaining']
                    ],
                    'nutrition_breakdown' => $item['nutrition_breakdown'] ?? [],
                    'meal_types' => $item['meal_types'] ?? [],
                    'logs_count' => [
                        'food' => $item['food_logs_count'] ?? 0,
                        'exercise' => $item['exercise_logs_count'] ?? 0
                    ]
                ];
            }),
            'meta' => [
                'start_date' => $this->collection->first()['date'] ?? null,
                'end_date' => $this->collection->last()['date'] ?? null,
                'days_count' => $this->collection->count(),
            ]
        ];
    }
}
