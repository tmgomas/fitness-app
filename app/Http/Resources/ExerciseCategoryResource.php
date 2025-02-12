<?php
// app/Http/Resources/ExerciseCategoryResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'measurement_type' => $this->measurement_type,
            'exercises' => ExerciseResource::collection($this->whenLoaded('exercises')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
