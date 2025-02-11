<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeasurementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->measurement_id,
            'measurements' => [
                'chest' => $this->chest,
                'waist' => $this->waist,
                'hips' => $this->hips,
                'arms' => $this->arms,
                'thighs' => $this->thighs,
            ],
            'recorded_at' => $this->recorded_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
