<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Currency;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\CurrenciesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreLookupTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_countries_and_currencies_seeders_insert_required_demo_data(): void
    {
        $this->seed([
            CountriesSeeder::class,
            CurrenciesSeeder::class,
        ]);

        $this->assertDatabaseCount('countries', 4);
        $this->assertDatabaseCount('currencies', 4);

        $this->assertDatabaseHas('countries', ['iso2' => 'EG', 'name' => 'Egypt', 'status' => 'active']);
        $this->assertDatabaseHas('countries', ['iso2' => 'SA', 'name' => 'Saudi Arabia', 'status' => 'active']);
        $this->assertDatabaseHas('countries', ['iso2' => 'AE', 'name' => 'United Arab Emirates', 'status' => 'active']);
        $this->assertDatabaseHas('countries', ['iso2' => 'US', 'name' => 'United States', 'status' => 'active']);

        $this->assertDatabaseHas('currencies', ['code' => 'EGP', 'name' => 'Egyptian Pound', 'status' => 'active']);
        $this->assertDatabaseHas('currencies', ['code' => 'SAR', 'name' => 'Saudi Riyal', 'status' => 'active']);
        $this->assertDatabaseHas('currencies', ['code' => 'AED', 'name' => 'UAE Dirham', 'status' => 'active']);
        $this->assertDatabaseHas('currencies', ['code' => 'USD', 'name' => 'US Dollar', 'status' => 'active']);
    }

    public function test_country_and_currency_factories_can_create_records(): void
    {
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();

        $this->assertDatabaseHas('countries', ['id' => $country->id]);
        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
    }
}
