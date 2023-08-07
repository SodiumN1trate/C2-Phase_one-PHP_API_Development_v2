<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $table = 'concerts';

    public $timestamps = false;

    protected $fillable = [
        'artist',
        'location_id',
    ];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function shows() {
        return $this->hasMany(Show::class)->orderBy('start');
    }
}
