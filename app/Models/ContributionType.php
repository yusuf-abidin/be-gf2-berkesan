<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function periods(): HasMany
    {
        return $this->hasMany(ContributionPeriod::class, 'contribution_type_id');
    }
}
