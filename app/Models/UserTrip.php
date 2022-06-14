<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'origin',
        'destination',
        'start_trip',
        'end_trip',
        'trip_type_id',
        'description'
    ];

    public function cityOrigin() {
        return $this->belongsTo(City::class, 'origin');
    }

    public function cityDeparture() {
        return $this->belongsTo(City::class, 'destination');
    }

    public function tripType() {
        return $this->belongsTo(TripType::class, 'trip_type_id');
    }
}
