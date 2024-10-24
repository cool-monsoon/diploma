<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Show;
use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rows_number',
        'seats_number',
        'standard_seat_price',
        'vip_seat_price',
        'is_active',
    ];

    protected $hidden = [
        'created_up',
        'updated_at',
    ];

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
