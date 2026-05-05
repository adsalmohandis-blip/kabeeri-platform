<?php

namespace Tests\Feature;

use App\Models\FeatureFlag;
use App\Models\FeatureFlagOverride;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Core\Services\FeatureFlagService;
use Database\Seeders\FeatureFlagsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_flags_seeder_adds_v1_flags(): void
    {
        $this->seed(FeatureFlagsSeeder::class);

        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_cms']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_media_library']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_theme_foundation']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_package_foundation']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_rabet_foundation']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_onboarding_basic']);
        $this->assertDatabaseHas('feature_flags', ['key' => 'enable_activity_logs']);
    }

    public function test_feature_flag_service_resolves_default_and_override(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $flag = FeatureFlag::query()->create([
            'key' => 'enable_cms',
            'default_value' => false,
            'status' => 'active',
        ]);

        $service = app(FeatureFlagService::class);

        $this->assertFalse($service->isEnabled('enable_cms'));

        FeatureFlagOverride::query()->create([
            'feature_flag_id' => $flag->id,
            'organization_id' => $organization->id,
            'scope_type' => null,
            'scope_id' => null,
            'value' => true,
        ]);

        $this->assertTrue($service->isEnabled('enable_cms', organizationId: $organization->id));

        FeatureFlagOverride::query()->create([
            'feature_flag_id' => $flag->id,
            'organization_id' => $organization->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'value' => false,
        ]);

        $this->assertFalse(
            $service->isEnabled(
                'enable_cms',
                organizationId: $organization->id,
                scopeType: 'site',
                scopeId: $site->id,
            ),
        );
    }
}
