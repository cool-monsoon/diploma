<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Show;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id',
        'hall_id',
        'seat_id',
        'is_booked',
    ];

    protected $casts = [
        'is_booked' => 'boolean',
    ];

    protected $attributes = [
        'is_booked' => false,
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
