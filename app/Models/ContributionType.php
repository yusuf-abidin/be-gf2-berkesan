<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function periods(): HasMany
    {
        return $this->hasMany(ContributionPeriod::class, 'contribution_type_id');
    }
}
