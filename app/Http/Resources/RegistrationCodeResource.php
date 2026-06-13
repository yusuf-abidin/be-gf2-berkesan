<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s.v'),
            'usage_limit' => $this->usage_limit,
            'current_usage' => $this->current_usage,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s.v'),
        ];
    }
}
