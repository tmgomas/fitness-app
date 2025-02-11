<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HealthDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->health_id,
            'height' => $this->height,
            'weight' => $this->weight,
            'bmi' => $this->bmi,
            'blood_type' => $this->blood_type,
            'medical_conditions' => $this->medical_conditions,
            'recorded_at' => $this->recorded_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
