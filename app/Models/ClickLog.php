<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'operator_slug',
        'country_code',
        'ip_hash',
        'user_agent_hash',
        'referer',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function scopeForOperator($query, string $operatorSlug)
    {
        return $query->where('operator_slug', $operatorSlug);
    }

    public function scopeForCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('clicked_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('clicked_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('clicked_at', now()->month)
                     ->whereYear('clicked_at', now()->year);
    }
}