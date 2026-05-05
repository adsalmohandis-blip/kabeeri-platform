<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Modules\Core\Services\PackageCatalogService;
use Database\Seeders\PackagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageCatalogFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_official_packages_can_be_seeded_idempotently(): void
    {
        $this->seed(PackagesSeeder::class);
        $this->seed(PackagesSeeder::class);

        $this->assertSame(2, Package::query()->count());
        $this->assertDatabaseHas('packages', [
            'key' => 'kabeeri.forms',
            'publisher_type' => 'official',
            'status' => 'active',
        ]);
    }

    public function test_packages_can_be_listed_and_filtered(): void
    {
        $this->seed(PackagesSeeder::class);

        $packages = app(PackageCatalogService::class)->list([
            'category' => 'commerce',
            'package_type' => 'module',
        ]);

        $this->assertSame(['kabeeri.commerce-lite'], $packages->pluck('key')->all());
    }
}
