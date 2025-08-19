<?php

namespace App\Console\Commands;

use App\Services\GeolocationService;
use Illuminate\Console\Command;

class UpdateGeoDatabase extends Command
{
    protected $signature = 'geo:update-database';
    protected $description = 'Update MaxMind GeoLite2 database';

    public function handle(GeolocationService $geolocationService)
    {
        $this->info('Downloading GeoLite2 database...');
        
        if ($geolocationService->downloadDatabase()) {
            $this->info('GeoLite2 database updated successfully!');
            return Command::SUCCESS;
        }
        
        $this->error('Failed to update GeoLite2 database. Check logs for details.');
        return Command::FAILURE;
    }
}