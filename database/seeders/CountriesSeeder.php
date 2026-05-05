<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Egypt',
                'iso2' => 'EG',
                'iso3' => 'EGY',
                'phone_code' => '20',
                'status' => 'active',
            ],
            [
                'name' => 'Saudi Arabia',
                'iso2' => 'SA',
                'iso3' => 'SAU',
                'phone_code' => '966',
                'status' => 'active',
            ],
            [
                'name' => 'United Arab Emirates',
                'iso2' => 'AE',
                'iso3' => 'ARE',
                'phone_code' => '971',
                'status' => 'active',
            ],
            [
                'name' => 'United States',
                'iso2' => 'US',
                'iso3' => 'USA',
                'phone_code' => '1',
                'status' => 'active',
            ],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(
                ['iso2' => $country['iso2']],
                $country,
            );
        }
    }
}
