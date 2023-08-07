<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'city',
        'zip',
        'country',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
