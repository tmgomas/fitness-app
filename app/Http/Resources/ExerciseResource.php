<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new ExerciseCategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'description' => $this->description,
            'difficulty_level' => $this->difficulty_level,
            'image_url' => $this->image_url,
            'calories_per_minute' => $this->calories_per_minute,
            'calories_per_km' => $this->calories_per_km,
            'requires_distance' => $this->requires_distance,
            'requires_heartrate' => $this->requires_heartrate,
            'recommended_intensity' => $this->recommended_intensity,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
