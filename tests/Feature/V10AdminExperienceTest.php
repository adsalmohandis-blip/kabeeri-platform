<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Ui\V10AdminExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V10AdminExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_v10_admin_config_defines_pages_modules_workspaces_and_permissions(): void
    {
        $config = config('kabeeri_admin');

        $this->assertSame('V10', $config['version']);
        $this->assertCount(6, $config['pages']);
        $this->assertGreaterThanOrEqual(10, count($config['module_groups']));
        $this->assertCount(12, $config['workspace_homes']);
        $this->assertGreaterThanOrEqual(6, count($config['permission_aware_navigation']['blocked_actions']));
        $this->assertSame('Filament is the internal admin runtime. Public marketing remains outside V10.', $config['rules']['runtime']);
    }

    public function test_v10_page_and_quick_action_routes_are_registered(): void
    {
        foreach (config('kabeeri_admin.pages') as $page) {
            $this->assertTrue(Route::has($page['route']), "Missing V10 page route [{$page['route']}].");
        }

        foreach (config('kabeeri_admin.quick_actions') as $action) {
            $this->assertTrue(Route::has($action['route']), "Missing V10 quick action route [{$action['route']}].");
        }
    }

    public function test_authenticated_admin_can_render_v10_command_pages(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (config('kabeeri_admin.pages') as $page) {
            $this->get(route($page['route']))
                ->assertOk()
                ->assertSee(__('kabeeri.brand.name').' V10 Admin Command')
                ->assertSee($page['label']);
        }
    }

    public function test_v10_admin_experience_reports_workspaces_and_blocked_actions(): void
    {
        $data = V10AdminExperience::all();

        $this->assertCount(12, $data['workspaces']);
        $this->assertTrue(collect($data['workspaces'])->contains('key', 'developer'));
        $this->assertTrue(collect($data['quick_actions'])->contains('permission', 'billing.manage'));
        $this->assertTrue(collect($data['permission_navigation']['blocked_actions'])->contains('permission', 'plugin.install'));
    }

    public function test_v10_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V10AdminExperience::validationReport();

        $this->assertSame([], $report['missing_page_routes']);
        $this->assertSame([], $report['missing_quick_action_routes']);
        $this->assertTrue($report['v9_ready']);
        $this->assertTrue(V10AdminExperience::isReleaseCandidateReady());
    }
}
