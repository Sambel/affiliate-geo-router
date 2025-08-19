<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'country_id',
        'url',
        'status',
        'priority',
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'integer',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForOperatorAndCountry($query, $operatorId, $countryId)
    {
        return $query->where('operator_id', $operatorId)
                     ->where('country_id', $countryId);
    }
}