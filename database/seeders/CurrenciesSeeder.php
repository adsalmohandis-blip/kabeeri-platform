<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => 'EGP',
                'decimals' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => 'SAR',
                'decimals' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'AED',
                'decimals' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'decimals' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate(
                ['code' => $currency['code']],
                $currency,
            );
        }
    }
}
