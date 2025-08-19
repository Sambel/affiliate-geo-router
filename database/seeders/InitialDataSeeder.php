<?php

namespace Database\Seeders;

use App\Models\AffiliateLink;
use App\Models\Country;
use App\Models\Operator;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['iso_code' => 'FR', 'name' => 'France', 'status' => 'active'],
            ['iso_code' => 'US', 'name' => 'United States', 'status' => 'active'],
            ['iso_code' => 'GB', 'name' => 'United Kingdom', 'status' => 'active'],
            ['iso_code' => 'DE', 'name' => 'Germany', 'status' => 'active'],
            ['iso_code' => 'ES', 'name' => 'Spain', 'status' => 'active'],
            ['iso_code' => 'IT', 'name' => 'Italy', 'status' => 'active'],
            ['iso_code' => 'CA', 'name' => 'Canada', 'status' => 'active'],
            ['iso_code' => 'AU', 'name' => 'Australia', 'status' => 'active'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }

        $operators = [
            [
                'name' => 'Bet365',
                'slug' => 'bet365',
                'status' => 'active',
                'default_url' => 'https://www.bet365.com',
            ],
            [
                'name' => 'William Hill',
                'slug' => 'william-hill',
                'status' => 'active',
                'default_url' => 'https://www.williamhill.com',
            ],
            [
                'name' => 'Unibet',
                'slug' => 'unibet',
                'status' => 'active',
                'default_url' => 'https://www.unibet.com',
            ],
        ];

        foreach ($operators as $operatorData) {
            $operator = Operator::create($operatorData);

            $france = Country::where('iso_code', 'FR')->first();
            if ($france) {
                AffiliateLink::create([
                    'operator_id' => $operator->id,
                    'country_id' => $france->id,
                    'url' => $operatorData['default_url'] . '/fr?aff=12345',
                    'status' => 'active',
                    'priority' => 10,
                ]);
            }

            $uk = Country::where('iso_code', 'GB')->first();
            if ($uk) {
                AffiliateLink::create([
                    'operator_id' => $operator->id,
                    'country_id' => $uk->id,
                    'url' => $operatorData['default_url'] . '/uk?aff=12345',
                    'status' => 'active',
                    'priority' => 10,
                ]);
            }
        }

        $this->command->info('Initial data seeded successfully!');
    }
}