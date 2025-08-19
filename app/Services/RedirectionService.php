<?php

namespace App\Services;

use App\Jobs\LogClickJob;
use App\Models\AffiliateLink;
use App\Models\Country;
use App\Models\Operator;
use Illuminate\Support\Facades\Cache;

class RedirectionService
{
    protected GeolocationService $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    public function getRedirectUrl(string $operatorSlug, string $ip, ?string $userAgent = null, ?string $referer = null): ?string
    {
        $operator = $this->getOperator($operatorSlug);
        
        if (!$operator || !$operator->isActive()) {
            return null;
        }

        $countryCode = $this->geolocationService->getCountryCode($ip);
        $url = $this->getAffiliateUrl($operator, $countryCode);

        $this->logClick($operatorSlug, $countryCode, $ip, $userAgent, $referer);

        return $url;
    }

    protected function getOperator(string $slug): ?Operator
    {
        return Cache::remember(
            "operator:{$slug}",
            1800,
            fn() => Operator::where('slug', $slug)->first()
        );
    }

    protected function getAffiliateUrl(Operator $operator, string $countryCode): string
    {
        $cacheKey = "affiliate_url:{$operator->id}:{$countryCode}";
        
        return Cache::remember($cacheKey, 1800, function () use ($operator, $countryCode) {
            $country = Country::where('iso_code', $countryCode)
                            ->where('status', 'active')
                            ->first();

            if (!$country) {
                return $operator->default_url;
            }

            $affiliateLink = AffiliateLink::where('operator_id', $operator->id)
                                         ->where('country_id', $country->id)
                                         ->where('status', 'active')
                                         ->orderBy('priority', 'desc')
                                         ->first();

            return $affiliateLink ? $affiliateLink->url : $operator->default_url;
        });
    }

    protected function logClick(string $operatorSlug, string $countryCode, string $ip, ?string $userAgent, ?string $referer): void
    {
        $ipHash = hash('sha256', $ip . config('app.key'));
        $userAgentHash = $userAgent ? hash('sha256', $userAgent . config('app.key')) : null;
        
        if ($referer) {
            $parsedUrl = parse_url($referer);
            $referer = $parsedUrl['host'] ?? null;
        }

        LogClickJob::dispatch([
            'operator_slug' => $operatorSlug,
            'country_code' => $countryCode,
            'ip_hash' => $ipHash,
            'user_agent_hash' => $userAgentHash,
            'referer' => $referer,
            'clicked_at' => now(),
        ]);
    }

    public function clearCache(string $operatorSlug): void
    {
        $operator = Operator::where('slug', $operatorSlug)->first();
        
        if ($operator) {
            Cache::forget("operator:{$operatorSlug}");
            
            $countries = Country::all();
            foreach ($countries as $country) {
                Cache::forget("affiliate_url:{$operator->id}:{$country->iso_code}");
            }
        }
    }
}