<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationSeatRowResource extends JsonResource
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
            'name' => $this->name,
            'seats' => [
                'total' => $this->seats->count(),
                'unavailable' => $this->seats()->whereNotNull('ticket_id')->orderBy('number', 'asc')->get()->pluck('id'),
            ],
        ];
    }
}
