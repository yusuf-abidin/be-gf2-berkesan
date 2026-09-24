<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coordinator extends Model
{
    protected $fillable = [
        'name',
        'managed_blocks'
    ];

    public function residents(): hasMany
    {
        return $this->hasMany(Resident::class);
    }
}
