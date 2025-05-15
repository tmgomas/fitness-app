<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAgreementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'agreement' => new AgreementResource($this->whenLoaded('agreement')),
            'accepted_at' => $this->accepted_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}