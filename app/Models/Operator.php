<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'default_url',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}