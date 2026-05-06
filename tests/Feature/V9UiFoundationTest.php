<?php

namespace Tests\Feature;

use App\Support\Ui\V9UiFoundation;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V9UiFoundationTest extends TestCase
{
    public function test_v9_foundation_config_contains_required_runtime_boundaries_and_tokens(): void
    {
        $foundation = V9UiFoundation::all();

        $this->assertSame('V9', $foundation['version']);
        $this->assertSame('Next.js + React + TypeScript + Tailwind', $foundation['decision']['public_runtime_target']);
        $this->assertSame('Filament + Livewire inside Laravel', $foundation['decision']['admin_runtime']);
        $this->assertArrayHasKey('laravel_backend', $foundation['boundaries']);
        $this->assertArrayHasKey('next_public_runtime', $foundation['boundaries']);
        $this->assertArrayHasKey('olive', V9UiFoundation::designTokens()['colors']);
        $this->assertContains('PricingTable', $foundation['component_standards']);
    }

    public function test_v9_admin_spaces_and_external_audiences_are_defined(): void
    {
        $this->assertCount(12, V9UiFoundation::adminSpaces());
        $this->assertArrayHasKey('platform', V9UiFoundation::adminSpaces());
        $this->assertArrayHasKey('developer', V9UiFoundation::adminSpaces());
        $this->assertArrayHasKey('business_owner', V9UiFoundation::externalAudiences());
        $this->assertArrayHasKey('mall_visitor', V9UiFoundation::externalAudiences());
    }

    public function test_v9_current_route_registry_points_to_existing_named_routes(): void
    {
        foreach (V9UiFoundation::currentRoutes() as $route) {
            $this->assertTrue(Route::has($route['route']), "Missing V9 route registry route [{$route['route']}].");
        }
    }

    public function test_v9_documentation_and_css_tokens_exist(): void
    {
        $this->assertFileExists(base_path('docs/kabeeri/ui/V9_UI_FOUNDATION.md'));
        $this->assertFileExists(base_path('docs/kabeeri/ui/ROUTE_PAGE_REGISTRY.md'));
        $this->assertFileExists(base_path('docs/kabeeri/ui/UI_SMOKE_TEST_STRATEGY.md'));
        $this->assertFileExists(base_path('docs/kabeeri/ui/NEXT_PUBLIC_RUNTIME_PLAN.md'));

        $this->assertStringContainsString('Kabeeri Marketplace is internal extensions', file_get_contents(base_path('docs/kabeeri/ui/V9_UI_FOUNDATION.md')));
        $this->assertStringContainsString('--kbr-olive', file_get_contents(resource_path('css/app.css')));
    }

    public function test_v9_release_candidate_report_is_ready(): void
    {
        $report = V9UiFoundation::validationReport();

        $this->assertSame([], $report['missing_routes']);
        $this->assertTrue($report['has_runtime_decision']);
        $this->assertTrue($report['has_design_tokens']);
        $this->assertTrue(V9UiFoundation::isReleaseCandidateReady());
    }
}
