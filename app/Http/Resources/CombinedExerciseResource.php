<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CombinedExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Common fields for both exercise types
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'difficulty_level' => $this->difficulty_level ?? 'medium',
            'image_url' => $this->image_url,
            'calories_per_minute' => (float) $this->calories_per_minute,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];

        // Add is_custom flag from resource item or default to false
        $data['is_custom'] = $this->is_custom ?? false;

        // Fields specific to standard exercises
        if (!$data['is_custom']) {
            $data['category'] = new ExerciseCategoryResource($this->whenLoaded('category'));
            $data['requires_distance'] = (bool) $this->requires_distance;
            $data['requires_heartrate'] = (bool) $this->requires_heartrate;
            $data['calories_per_km'] = $this->when($this->calories_per_km, (float) $this->calories_per_km);
            $data['recommended_intensity'] = $this->recommended_intensity;
        }
        // Fields specific to custom exercises
        else {
            $data['user_id'] = $this->user_id;
        }

        return $data;
    }
}
