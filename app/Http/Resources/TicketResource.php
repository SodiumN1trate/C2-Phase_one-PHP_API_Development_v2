<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        dd($this->seat[0]->seatRow);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'row' => [
                'id' => $this->seat[0]->seatRow->id,
                'name' => $this->seat[0]->seatRow->name,
            ]
        ];
    }
}
