<?php

// app/Http/Resources/UserResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

