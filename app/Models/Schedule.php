<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'start_time',
        'end_time',
        'filled',
    ];

    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class);
    }

    public function scopeByBusy($query,$start_time,$end_time) 
    { 
        return $query->whereBetween('start_time', [$start_time, $end_time]) 
            ->orWhereBetween('end_time', [$start_time, $end_time]) 
            ->orWhereRaw('? BETWEEN start_time and end_time', [$start_time]) 
            ->orWhereRaw('? BETWEEN start_time and end_time', [$end_time]); 
    }
}
