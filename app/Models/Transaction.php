<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'period_id',
        'category_id',
        'contribution_period_id',
        'type',
        'name',
        'day',
        'quantity',
        'amount',
        'description',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contributionPeriod(): BelongsTo
    {
        return $this->belongsTo(ContributionPeriod::class, 'contribution_period_id');
    }
}
