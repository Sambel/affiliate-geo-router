<?php

namespace App\Console\Commands;

use App\Models\AffiliateLink;
use App\Models\Country;
use App\Models\Operator;
use Illuminate\Console\Command;

class SetupTestData extends Command
{
    protected $signature = 'setup:test-data';
    protected $description = 'Setup test data with multiple countries and affiliate links';

    public function handle()
    {
        $this->info('Setting up test data...');

        // Créer quelques pays
        $countries = [
            ['iso_code' => 'FR', 'name' => 'France'],
            ['iso_code' => 'US', 'name' => 'United States'],
            ['iso_code' => 'GB', 'name' => 'United Kingdom'],
            ['iso_code' => 'DE', 'name' => 'Germany'],
            ['iso_code' => 'ES', 'name' => 'Spain'],
            ['iso_code' => 'IT', 'name' => 'Italy'],
            ['iso_code' => 'CA', 'name' => 'Canada'],
            ['iso_code' => 'AU', 'name' => 'Australia'],
        ];

        foreach ($countries as $countryData) {
            Country::updateOrCreate(
                ['iso_code' => $countryData['iso_code']],
                $countryData + ['status' => 'active']
            );
        }

        // Créer quelques opérateurs
        $operators = [
            ['name' => 'Bet365', 'slug' => 'bet365'],
            ['name' => 'Betclic', 'slug' => 'betclic'],
            ['name' => 'Winamax', 'slug' => 'winamax'],
            ['name' => 'Unibet', 'slug' => 'unibet'],
        ];

        foreach ($operators as $operatorData) {
            Operator::updateOrCreate(
                ['slug' => $operatorData['slug']],
                $operatorData + [
                    'status' => 'active',
                    'default_url' => 'https://example.com/default'
                ]
            );
        }

        // Créer des liens affiliés pour différents pays
        $affiliateLinks = [
            // Bet365
            ['operator_slug' => 'bet365', 'country_code' => 'FR', 'url' => 'https://bet365.fr/?affiliate=test'],
            ['operator_slug' => 'bet365', 'country_code' => 'US', 'url' => 'https://bet365.com/us/?affiliate=test'],
            ['operator_slug' => 'bet365', 'country_code' => 'GB', 'url' => 'https://bet365.com/uk/?affiliate=test'],
            ['operator_slug' => 'bet365', 'country_code' => 'DE', 'url' => 'https://bet365.de/?affiliate=test'],

            // Betclic
            ['operator_slug' => 'betclic', 'country_code' => 'FR', 'url' => 'https://betclic.fr/?affiliate=test'],
            ['operator_slug' => 'betclic', 'country_code' => 'DE', 'url' => 'https://betclic.de/?affiliate=test'],
            ['operator_slug' => 'betclic', 'country_code' => 'ES', 'url' => 'https://betclic.es/?affiliate=test'],
            ['operator_slug' => 'betclic', 'country_code' => 'IT', 'url' => 'https://betclic.it/?affiliate=test'],

            // Winamax
            ['operator_slug' => 'winamax', 'country_code' => 'FR', 'url' => 'https://winamax.fr/?affiliate=test'],
            ['operator_slug' => 'winamax', 'country_code' => 'ES', 'url' => 'https://winamax.es/?affiliate=test'],

            // Unibet
            ['operator_slug' => 'unibet', 'country_code' => 'FR', 'url' => 'https://unibet.fr/?affiliate=test'],
            ['operator_slug' => 'unibet', 'country_code' => 'GB', 'url' => 'https://unibet.co.uk/?affiliate=test'],
            ['operator_slug' => 'unibet', 'country_code' => 'DE', 'url' => 'https://unibet.de/?affiliate=test'],
            ['operator_slug' => 'unibet', 'country_code' => 'CA', 'url' => 'https://unibet.ca/?affiliate=test'],
        ];

        foreach ($affiliateLinks as $linkData) {
            $operator = Operator::where('slug', $linkData['operator_slug'])->first();
            $country = Country::where('iso_code', $linkData['country_code'])->first();

            if ($operator && $country) {
                AffiliateLink::updateOrCreate(
                    [
                        'operator_id' => $operator->id,
                        'country_id' => $country->id,
                    ],
                    [
                        'url' => $linkData['url'],
                        'status' => 'active',
                        'priority' => 0,
                    ]
                );
            }
        }

        $this->info('Test data setup completed!');
        $this->info('Created ' . Country::count() . ' countries');
        $this->info('Created ' . Operator::count() . ' operators');
        $this->info('Created ' . AffiliateLink::count() . ' affiliate links');

        $this->info('');
        $this->info('Test URLs:');
        $this->info('- https://affiliate-geo-router-main-vvdgmn.laravel.cloud/bet365 (should redirect based on your IP country)');
        $this->info('- https://affiliate-geo-router-main-vvdgmn.laravel.cloud/betclic');
        $this->info('- https://affiliate-geo-router-main-vvdgmn.laravel.cloud/winamax');
        $this->info('- https://affiliate-geo-router-main-vvdgmn.laravel.cloud/unibet');
    }
}