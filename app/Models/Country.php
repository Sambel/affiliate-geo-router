<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'iso_code',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function affiliateLinks(): HasMany
    {
        return $this->hasMany(AffiliateLink::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}