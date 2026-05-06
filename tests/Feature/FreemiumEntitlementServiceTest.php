<?php

namespace Tests\Feature;

use App\Models\EntitlementOverride;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Platform\Services\EntitlementService;
use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreemiumEntitlementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_allows_one_app_but_blocks_additional_apps(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'free']);
        Site::factory()->create(['organization_id' => $organization->id]);

        $result = app(EntitlementService::class)->canCreateApp($organization);

        $this->assertFalse($result['allowed']);
        $this->assertSame('max_apps', $result['key']);
        $this->assertSame('limit_exceeded', $result['reason']);
        $this->assertSame(1, $result['limit']);
        $this->assertSame(1, $result['usage']);
    }

    public function test_free_plan_can_install_free_assets_but_not_pro_assets(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'free']);

        $this->assertTrue(app(EntitlementService::class)->canInstallTheme($organization, 'free')['allowed']);
        $this->assertTrue(app(EntitlementService::class)->canInstallPlugin($organization, 'free')['allowed']);
        $this->assertFalse(app(EntitlementService::class)->canInstallTheme($organization, 'pro')['allowed']);
        $this->assertFalse(app(EntitlementService::class)->canInstallPlugin($organization, 'paid')['allowed']);
    }

    public function test_active_entitlement_override_can_temporarily_allow_a_feature(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'free']);

        EntitlementOverride::query()->create([
            'organization_id' => $organization->id,
            'key' => 'can_install_pro_themes',
            'value_type' => 'boolean',
            'bool_value' => true,
            'behavior' => 'allow',
            'reason' => 'Manual customer success exception.',
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addDay(),
        ]);

        $result = app(EntitlementService::class)->canInstallTheme($organization, 'pro');

        $this->assertTrue($result['allowed']);
        $this->assertSame('can_install_pro_themes', $result['key']);
    }

    public function test_business_plan_has_larger_resource_limits(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'business']);
        Site::factory()->count(3)->create(['organization_id' => $organization->id]);

        $result = app(EntitlementService::class)->canCreateApp($organization);

        $this->assertTrue($result['allowed']);
        $this->assertSame(10, $result['limit']);
        $this->assertSame(3, $result['usage']);
    }
}
