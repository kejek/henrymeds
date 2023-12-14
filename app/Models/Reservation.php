<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'client_id',
        'reservation_slot',
        'confirmed',
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class);
    }
}
