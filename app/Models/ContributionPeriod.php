<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionPeriod extends Model
{
    protected $fillable = [
        'contribution_type_id',
        'month',
        'year',
        'amount_per_resident',
    ];

    public function contributionType(): BelongsTo
    {
        return $this->belongsTo(ContributionType::class, 'contribution_type_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContributionPayment::class, 'contribution_period_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'contribution_period_id');
    }
}
