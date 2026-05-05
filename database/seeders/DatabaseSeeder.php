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
            ModulesSeeder::class,
            V1DemoSeeder::class,
        ]);
    }
}
