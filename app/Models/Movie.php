<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'county',
        'poster',
    ];

    protected $hidden = [
        'created_up',
        'updated_at',
    ];

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class);
    }
}
