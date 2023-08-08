<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationSeat extends Model
{
    use HasFactory;


    protected $table = 'location_seats';

    public $timestamps = false;

    protected $fillable = [
        'location_seat_row_id',
        'number',
        'reservation_id',
        'ticket_id',
    ];

    public function seatRow() {
        return $this->belongsTo(LocationSeatRow::class, 'location_seat_row_id');
    }
}
