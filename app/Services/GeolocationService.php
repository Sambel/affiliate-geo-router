<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    protected ?Reader $reader = null;
    protected string $defaultCountry;

    public function __construct()
    {
        $this->defaultCountry = config('affiliate.default_country', 'FR');
        $this->initializeReader();
    }

    protected function initializeReader(): void
    {
        $dbPath = storage_path('app/geoip/GeoLite2-Country.mmdb');
        
        if (file_exists($dbPath)) {
            try {
                $this->reader = new Reader($dbPath);
            } catch (\Exception $e) {
                Log::error('Failed to initialize GeoIP reader: ' . $e->getMessage());
            }
        }
    }

    public function getCountryCode(string $ip): string
    {
        if ($this->isPrivateIp($ip)) {
            return $this->defaultCountry;
        }

        $cacheKey = 'geo:' . md5($ip);
        
        return Cache::remember($cacheKey, 3600, function () use ($ip) {
            // Essayer d'abord MaxMind si disponible
            if ($this->reader) {
                try {
                    $record = $this->reader->country($ip);
                    $countryCode = $record->country->isoCode;
                    if ($countryCode) {
                        return $countryCode;
                    }
                } catch (\Exception $e) {
                    Log::warning('MaxMind geolocation failed for IP ' . $ip . ': ' . $e->getMessage());
                }
            }

            // Fallback vers service gratuit ip-api.com
            try {
                $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'AffiliateGeoRouter/1.0'
                    ]
                ]));

                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['countryCode']) && $data['countryCode']) {
                        Log::info("Geolocation fallback used for IP {$ip}: {$data['countryCode']}");
                        return $data['countryCode'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Fallback geolocation failed for IP ' . $ip . ': ' . $e->getMessage());
            }

            return $this->defaultCountry;
        });
    }

    protected function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    public function downloadDatabase(): bool
    {
        $licenseKey = config('affiliate.maxmind_license_key');
        $userId = config('affiliate.maxmind_user_id');
        
        if (!$licenseKey || !$userId) {
            Log::warning('MaxMind credentials not configured');
            return false;
        }

        $url = sprintf(
            'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=%s&suffix=tar.gz',
            $licenseKey
        );

        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'geolite2');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FILE, fopen($tempFile, 'w'));
            curl_exec($ch);
            curl_close($ch);

            $phar = new \PharData($tempFile);
            $phar->extractTo(storage_path('app/geoip'), null, true);

            $files = glob(storage_path('app/geoip/GeoLite2-Country_*/GeoLite2-Country.mmdb'));
            if (!empty($files)) {
                rename($files[0], storage_path('app/geoip/GeoLite2-Country.mmdb'));
            }

            unlink($tempFile);
            
            $this->initializeReader();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to download GeoIP database: ' . $e->getMessage());
            return false;
        }
    }
}