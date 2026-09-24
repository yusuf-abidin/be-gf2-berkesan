<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'day' => $this->day,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'month' => $this->month,
            'year' => $this->year,
            'quantity' => $this->quantity,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s.v'),
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
        ];
    }
}
