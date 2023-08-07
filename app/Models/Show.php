<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $table = 'shows';

    public $timestamps = false;
    protected $fillable = [
        'concert_id',
        'start',
        'end',
    ];


    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function concert() {
        return $this->belongsTo(Concert::class);
    }
}
