<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class Patient extends Authenticatable implements JWTSubject
{
    use HasFactory;


    protected $fillable = [
        'patient_id_ext',
        'name',
        'surname',
        'sex',
        'birth_date',
        'login',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];

 /*
     |--------------------------------------------------------------------------
     | Relacje
     |--------------------------------------------------------------------------
     */

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Pełne imię i nazwisko.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->surname}";
    }

    /**
     * Krótka płeć do API ('m' / 'f').
     */
    public function getSexShortAttribute(): ?string
    {
        return match (strtolower((string) $this->sex)) {
            'male'   => 'm',
            'female' => 'f',
            default  => null,
        };
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
