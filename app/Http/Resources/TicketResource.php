<?php

namespace App\Http\Resources;

use App\Models\LocationSeat;
use App\Models\LocationSeatRow;
use App\Models\Ticket;
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
//        $ticket = Ticket::find($this->id);
//        $row = LocationSeat::find($this->seat[0]->id);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'row' => [
                'id' => $this->seat->seatRow->id,
                'name' => $this->seat->seatRow->name,
            ],
            'seat' => $this->seat->number,
            'show' => [
                'id' => $this->seat->seatRow->show->id,
                'start' => $this->seat->seatRow->show->start,
                'end' => $this->seat->seatRow->show->end,
                'concert' => [
                    'id' => $this->seat->seatRow->show->concert->id,
                    'artist' => $this->seat->seatRow->show->concert->artist,
                    'location' => [
                        'id' => $this->seat->seatRow->show->concert->location->id,
                        'name' => $this->seat->seatRow->show->concert->location->name,
                    ],
                ],
            ],
        ];
    }
}
