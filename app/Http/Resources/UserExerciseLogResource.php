<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExerciseLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'exercise_id' => $this->exercise_id,
            'start_time' => $this->start_time->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time->format('Y-m-d H:i:s'),
            'duration_minutes' => (float) $this->duration_minutes,
            'distance' => $this->when($this->distance !== null, (float) $this->distance),
            'distance_unit' => $this->when($this->distance !== null, $this->distance_unit),
            'calories_burned' => (float) $this->calories_burned,
            'avg_heart_rate' => $this->when($this->avg_heart_rate !== null, (float) $this->avg_heart_rate),
            'intensity_level' => $this->intensity_level,
            'notes' => $this->notes,

            'exercise' => $this->when($this->relationLoaded('exercise'), function () {
                return [
                    'id' => $this->exercise->id,
                    'name' => $this->exercise->name,
                    'description' => $this->exercise->description,
                    'difficulty_level' => $this->exercise->difficulty_level,
                    'calories_per_minute' => (float) $this->exercise->calories_per_minute,
                    'calories_per_km' => $this->when(
                        $this->exercise->calories_per_km !== null,
                        (float) $this->exercise->calories_per_km
                    ),
                    'category' => $this->when($this->exercise->relationLoaded('category'), function () {
                        return [
                            'id' => $this->exercise->category->id,
                            'name' => $this->exercise->category->name,
                            'description' => $this->exercise->category->description
                        ];
                    })
                ];
            }),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
