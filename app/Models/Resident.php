<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resident extends Model
{
    protected $fillable = [
        'coordinator_id',
        'name',
        'block',
        'house_number',
        'status',
        'phone',
    ];

    public function coordinator() : BelongsTo
    {
        return $this->belongsTo(Coordinator::class);
    }
}
