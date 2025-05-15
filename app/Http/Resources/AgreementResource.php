<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AgreementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'version' => $this->version,
            'is_active' => $this->is_active,
            'effective_date' => $this->effective_date->format('Y-m-d'),
            'summary' => $this->summary,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}