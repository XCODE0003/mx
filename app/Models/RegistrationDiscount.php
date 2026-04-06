<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationDiscount extends Model
{
    protected $fillable = [
        'discount_percent',
        'registration_from',
        'registration_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'registration_from' => 'date',
            'registration_to'   => 'date',
            'is_active'         => 'boolean',
        ];
    }
}
