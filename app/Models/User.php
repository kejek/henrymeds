<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNan;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isClient(): bool
    {
        $client = $this->client()->get();
        if ($client->isEmpty()){
            return false;
        }

        return true;
    }

    public function isProvider(): bool
    {
        $provider = $this->provider()->get();

        if ($provider->isEmpty()){
            return false;
        }
        return true;
    }

    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }
}
