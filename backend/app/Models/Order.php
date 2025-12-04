<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id_ext',
        'patient_id',
    ];
 /*
     |--------------------------------------------------------------------------
     | Relacje
     |--------------------------------------------------------------------------
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
