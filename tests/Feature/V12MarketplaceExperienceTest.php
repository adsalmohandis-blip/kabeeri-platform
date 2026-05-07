<?php

namespace Tests\Feature;

use App\Support\Ui\V12MarketplaceExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V12MarketplaceExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_v12_marketplace_config_defines_pages_catalogs_lifecycle_and_qa(): void
    {
        $config = config('kabeeri_marketplace');

        $this->assertSame('V12', $config['version']);
        $this->assertCount(19, $config['pages']);
        $this->assertGreaterThanOrEqual(3, count($config['themes']));
        $this->assertGreaterThanOrEqual(3, count($config['plugins']));
        $this->assertGreaterThanOrEqual(3, count($config['bundles']));
        $this->assertCount(9, $config['lifecycle_statuses']);
        $this->assertGreaterThanOrEqual(10, count($config['qa_checklists']['theme']));
        $this->assertGreaterThanOrEqual(12, count($config['qa_checklists']['plugin']));
        $this->assertContains('InstallPermissionPanel', $config['component_library']['components']);
    }

    public function test_v12_marketplace_and_developer_routes_are_registered(): void
    {
        foreach (config('kabeeri_marketplace.pages') as $page) {
            $this->assertTrue(Route::has($page['route']), "Missing V12 route [{$page['route']}].");
        }

        foreach (config('kabeeri_marketplace.admin_routes') as $route) {
            $this->assertTrue(Route::has($route), "Missing V12 admin route [{$route}].");
        }
    }

    public function test_v12_marketplace_and_developer_pages_render(): void
    {
        foreach (config('kabeeri_marketplace.pages') as $key => $page) {
            $this->get(route($page['route'], $this->routeParametersFor($key)))
                ->assertOk()
                ->assertSee(__('kabeeri.brand.name').' V12 Marketplace Studio')
                ->assertSee($page['label']);
        }
    }

    public function test_theme_detail_explains_preview_component_runtime_and_qa(): void
    {
        $this->get(route('marketplace.themes.show', 'kabeeri-atlas'))
            ->assertOk()
            ->assertSee('kabeeri Atlas')
            ->assertSee('Theme Detail and Preview')
            ->assertSee('React Component Runtime Mapping')
            ->assertSee('Theme Manifest to React Components')
            ->assertSee('API-only rendering');
    }

    public function test_plugin_detail_explains_manifest_permissions_signing_and_compatibility(): void
    {
        $this->get(route('marketplace.plugins.show', 'commerce-pulse-pack'))
            ->assertOk()
            ->assertSee('Commerce Pulse Pack')
            ->assertSee('Plugin Detail and Compatibility')
            ->assertSee('Manifest Permissions')
            ->assertSee('orders.write')
            ->assertSee('rollback safe');
    }

    public function test_governance_docs_qa_and_revenue_pages_explain_v12_operating_model(): void
    {
        $this->get(route('marketplace.review-status'))
            ->assertOk()
            ->assertSee('Package Lifecycle')
            ->assertSee('under_review')
            ->assertSee('needs_changes');

        $this->get(route('developers.qa'))
            ->assertOk()
            ->assertSee('Theme QA Publishing Checklist UI')
            ->assertSee('Plugin QA Security Permission Checklist UI');

        $this->get(route('marketplace.licensing'))
            ->assertOk()
            ->assertSee('Marketplace Licensing Pricing and Revenue Share UI')
            ->assertSee('Revenue Share');
    }

    public function test_v12_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V12MarketplaceExperience::validationReport();

        $this->assertSame([], $report['missing_routes']);
        $this->assertSame([], $report['missing_admin_routes']);
        $this->assertSame([], $report['missing_database_tables']);
        $this->assertTrue($report['v9_ready']);
        $this->assertTrue($report['v10_ready']);
        $this->assertTrue($report['v11_ready']);
        $this->assertTrue($report['docs']['marketplace_ux']);
        $this->assertTrue($report['docs']['release_report']);
        $this->assertTrue(V12MarketplaceExperience::isReleaseCandidateReady());
    }

    /**
     * @return array<int, string>
     */
    private function routeParametersFor(string $page): array
    {
        return match ($page) {
            'theme_detail' => ['kabeeri-atlas'],
            'plugin_detail' => ['commerce-pulse-pack'],
            default => [],
        };
    }
}
