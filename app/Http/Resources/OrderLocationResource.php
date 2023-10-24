<?php

namespace App\Http\Resources;

use App\Models\OrderLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OrderLocation
 */
class OrderLocationResource extends JsonResource
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
            'order_id' => $this->order_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
