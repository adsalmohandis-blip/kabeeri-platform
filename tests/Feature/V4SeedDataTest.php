<?php

namespace Tests\Feature;

use App\Models\AgencyPartnerProfile;
use App\Models\GrowthReferral;
use App\Models\MallMirrorBusiness;
use App\Models\Package;
use App\Models\PartnerStorefront;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\V4DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V4SeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_v4_demo_seed_data_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(V4DemoSeeder::class);

        $this->assertSame(1, MallMirrorBusiness::query()->where('slug', 'v4-demo-business')->count());
        $this->assertSame(1, Package::query()->where('key', 'kabeeri.v4-demo-package')->count());
        $this->assertSame(1, AgencyPartnerProfile::query()->where('slug', 'v4-demo-agency')->count());
        $this->assertSame(1, PartnerStorefront::query()->where('slug', 'v4-demo-partner-storefront')->count());
        $this->assertSame(1, GrowthReferral::query()->where('code', 'REF-V4-DEMO')->count());
    }
}
