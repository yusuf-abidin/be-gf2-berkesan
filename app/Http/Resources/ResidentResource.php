<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coordinator_id' => $this->coordinator_id,
            'name' => $this->name,
            'block' => $this->block,
            'house_number' => $this->house_number,
            'status' => $this->status,
            'phone' => $this->phone
        ];
    }
}
