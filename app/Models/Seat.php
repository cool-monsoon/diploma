<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hall;
use App\Models\Show;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'seat_type',
        'row_name',
        'seat_name',
    ];

    const TYPE_STANDARD = 'standard';
    const TYPE_VIP = 'vip';
    const TYPE_DISABLED = 'disabled';

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isBookedForShow($showId)
    {
        return $this->bookings()->where('show_id', $showId)->exists();
    }
}
