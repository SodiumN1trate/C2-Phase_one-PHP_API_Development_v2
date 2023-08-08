<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationSeatRow extends Model
{
    use HasFactory;

    protected $table = 'location_seat_rows';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'order',
        'show_id',
    ];

    public function seats() {
        return $this->hasMany(LocationSeat::class);
    }

    public function show() {
        return $this->belongsTo(Show::class);
    }
}
