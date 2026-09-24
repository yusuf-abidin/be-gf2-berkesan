<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributionPayment extends Model
{
    protected $fillable = [
        'contribution_period_id',
        'resident_id',
        'amount_paid',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function contributionPeriod(): BelongsTo
    {
        return $this->belongsTo(ContributionPeriod::class, 'contribution_period_id');
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }
}
