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

    public function test_feature_flags_seeder_adds_v2_flags_with_safe_defaults(): void
    {
        $this->seed(FeatureFlagsSeeder::class);
        $this->seed(FeatureFlagsSeeder::class);

        $expectedFlags = [
            'enable_cms_menus',
            'enable_cms_redirects',
            'enable_seo_tools',
            'enable_forms',
            'enable_lead_capture',
            'enable_wordpress_import',
            'enable_theme_catalog',
            'enable_theme_app_recipes',
            'enable_package_catalog',
            'enable_plugin_bundles',
            'enable_commerce_lite',
            'enable_external_source_registry',
        ];

        foreach ($expectedFlags as $key) {
            $this->assertDatabaseHas('feature_flags', [
                'key' => $key,
                'default_value' => false,
                'status' => 'active',
            ]);

            $this->assertSame(
                1,
                FeatureFlag::query()->where('key', $key)->count(),
                "Feature flag [{$key}] should be seeded idempotently.",
            );
        }
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

    public function test_feature_flags_seeder_adds_v3_flags_with_safe_defaults(): void
    {
        $this->seed(FeatureFlagsSeeder::class);
        $this->seed(FeatureFlagsSeeder::class);

        $expectedFlags = [
            'enable_crm_operations',
            'enable_service_requests',
            'enable_quotations',
            'enable_invoicing',
            'enable_manual_payments',
            'enable_inventory_lite',
            'enable_purchasing',
            'enable_accounting_starter',
            'enable_people_operations',
            'enable_projects_tasks',
            'enable_workflow_basic',
            'enable_reports_basic',
            'enable_dashboard_widgets',
        ];

        foreach ($expectedFlags as $key) {
            $this->assertDatabaseHas('feature_flags', [
                'key' => $key,
                'default_value' => false,
                'status' => 'active',
            ]);

            $this->assertSame(1, FeatureFlag::query()->where('key', $key)->count());
        }
    }
}
