<?php

namespace Tests\Feature;

use App\Support\Ui\V14UiReleaseCandidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V14UiReleaseCandidateTest extends TestCase
{
    use RefreshDatabase;

    public function test_v14_quality_config_defines_release_candidate_gates(): void
    {
        $config = config('kabeeri_ui_quality');

        $this->assertSame('V14', $config['version']);
        $this->assertGreaterThanOrEqual(5, count($config['route_groups']));
        $this->assertGreaterThanOrEqual(3, count($config['accessibility']));
        $this->assertGreaterThanOrEqual(4, count($config['responsive']));
        $this->assertGreaterThanOrEqual(3, count($config['security_permission']));
        $this->assertGreaterThanOrEqual(10, count($config['quality_gates']));
        $this->assertSame('documented_not_scaffolded', $config['next_runtime']['status']);
    }

    public function test_v14_full_ui_route_inventory_has_no_missing_configured_routes(): void
    {
        $inventory = V14UiReleaseCandidate::routeInventory();
        $missing = collect($inventory['routes'])->where('exists', false)->values();

        $this->assertGreaterThanOrEqual(90, count($inventory['routes']));
        $this->assertSame(0, $missing->count(), 'Missing routes: '.$missing->pluck('route')->implode(', '));
        $this->assertTrue(Route::has('ui.release-candidate'));
        $this->assertTrue(collect($inventory['routes'])->contains('route', 'mall.trust'));
        $this->assertTrue(collect($inventory['routes'])->contains('route', 'marketplace.governance'));
        $this->assertTrue(collect($inventory['routes'])->contains('route', 'customer.dashboard'));
    }

    public function test_v14_quality_coverage_passes_core_qa_dimensions(): void
    {
        $coverage = V14UiReleaseCandidate::validationReport()['coverage'];

        foreach ([
            'accessibility',
            'rtl_arabic',
            'responsive',
            'navigation',
            'states',
            'security_permission',
            'performance_assets',
            'manual_cross_browser',
            'next_runtime',
            'permission_navigation',
            'progressive_disclosure',
            'design_system',
            'theme_qa',
            'plugin_qa',
            'mall_marketplace_separation',
            'product_narrative',
        ] as $gate) {
            $this->assertTrue($coverage[$gate]['ready'], "Coverage gate [{$gate}] is not ready.");
        }
    }

    public function test_v14_release_candidate_page_renders_quality_center(): void
    {
        $this->get(route('ui.release-candidate'))
            ->assertOk()
            ->assertSee('KABEERI V14 UI Release Candidate')
            ->assertSee('Full UI Route Inventory Verification')
            ->assertSee('Quality Coverage')
            ->assertSee('Marketplace vs Mall')
            ->assertSee('php artisan test');
    }

    public function test_v14_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V14UiReleaseCandidate::validationReport();

        $this->assertSame([], $report['missing_routes']);
        $this->assertTrue($report['previous_versions']['v9']);
        $this->assertTrue($report['previous_versions']['v10']);
        $this->assertTrue($report['previous_versions']['v11']);
        $this->assertTrue($report['previous_versions']['v12']);
        $this->assertTrue($report['previous_versions']['v13']);
        $this->assertTrue($report['docs']['qa_handoff']);
        $this->assertTrue($report['docs']['release_candidate']);
        $this->assertTrue(V14UiReleaseCandidate::isReleaseCandidateReady());
    }
}
