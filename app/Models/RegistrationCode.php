<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationCode extends Model
{
    protected $fillable = [
        'code',
        'expires_at',
        'usage_limit',
        'current_usage',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime'
        ];
    }
}
