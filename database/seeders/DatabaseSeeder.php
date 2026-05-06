<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountriesSeeder::class,
            CurrenciesSeeder::class,
            FeatureFlagsSeeder::class,
            PermissionsSeeder::class,
            RolesSeeder::class,
            ThemesSeeder::class,
            PackagesSeeder::class,
            FreemiumSeeder::class,
            PaymentMethodsSeeder::class,
            ModulesSeeder::class,
            V1DemoSeeder::class,
            V2DemoSeeder::class,
            V3DemoSeeder::class,
            V4DemoSeeder::class,
            V5DemoSeeder::class,
            V6DemoSeeder::class,
            V7DemoSeeder::class,
            V8DemoSeeder::class,
        ]);
    }
}
