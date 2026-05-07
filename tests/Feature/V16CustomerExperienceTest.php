<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\Organization;
use App\Models\Site;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Support\Ui\V16CustomerExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V16CustomerExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_v16_customer_routes_are_registered(): void
    {
        foreach (config('kabeeri_customer.routes') as $route) {
            $this->assertTrue(Route::has($route['name']), "Missing V16 route [{$route['name']}].");
        }
    }

    public function test_start_page_renders_customer_paths_and_theme_catalog(): void
    {
        $this->get('/start')
            ->assertOk()
            ->assertSee('KABEERI Customer Start')
            ->assertSee('Business Owner')
            ->assertSee('I need a builder')
            ->assertSee('Kabeeri Atlas');
    }

    public function test_customer_register_creates_user_profile_and_redirects_to_onboarding(): void
    {
        $response = $this->post('/register', [
            'name' => 'Customer One',
            'email' => 'customer@example.test',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
            'customer_path' => 'store_owner',
        ]);

        $response->assertRedirect(route('customer.onboarding', ['path' => 'store_owner']));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'customer@example.test']);
        $this->assertDatabaseHas('user_profiles', ['visibility' => 'private']);
        $this->assertTrue(Hash::check('password-123', User::query()->where('email', 'customer@example.test')->firstOrFail()->password));
    }

    public function test_customer_onboarding_provisions_workspace_site_theme_settings_and_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'password-123']);
        $this->actingAs($user);

        $response = $this->post(route('customer.onboarding.store'), [
            'customer_path' => 'needs_builder',
            'organization_name' => 'Acme Workspace',
            'company_name' => 'Acme Company',
            'site_name' => 'Acme Store',
            'app_type' => 'store',
            'theme_slug' => 'mall-window',
            'needs_builder_help' => '1',
            'developer_creator' => '1',
            'builder_request_note' => 'I need help building the store.',
        ]);

        $response->assertRedirect(route('customer.workspace'));

        $organization = Organization::query()->where('name', 'Acme Workspace')->firstOrFail();
        $site = Site::query()->where('name', 'Acme Store')->firstOrFail();

        $this->assertSame($organization->id, $site->organization_id);
        $this->assertSame('store', $site->metadata['v16_app_type']);
        $this->assertSame('active', $site->metadata['theme_install_status']);
        $this->assertSame('mall-window', $site->theme?->slug);
        $this->assertDatabaseHas('companies', ['trade_name' => 'Acme Company']);
        $this->assertGreaterThanOrEqual(1, ThemeSetting::query()->where('site_id', $site->id)->count());
        $this->assertGreaterThanOrEqual(1, ContentEntry::query()->where('site_id', $site->id)->count());
        $this->assertContains('needs_builder_help', $user->profile->refresh()->metadata['capabilities']);
        $this->assertContains('developer_creator', $user->profile->metadata['capabilities']);

        $this->get(route('customer.workspace'))
            ->assertOk()
            ->assertSee('Customer Dashboard')
            ->assertSee('Acme Store')
            ->assertSee('Mall Window');

        $this->get(route('customer.apps.show', $site))
            ->assertOk()
            ->assertSee('App is active')
            ->assertSee('Mall Window');
    }

    public function test_customer_dashboard_is_protected_and_foreign_app_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $this->get(route('customer.workspace'))->assertRedirect('/login');

        $this->actingAs($other)
            ->get(route('customer.apps.show', $site))
            ->assertForbidden();
    }

    public function test_customer_can_update_profile_capabilities(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('customer.capabilities.update'), [
            'capabilities' => ['marketer_partner', 'implementation_builder'],
            'capability_note' => 'I want to help customers build their apps.',
        ])->assertRedirect(route('customer.workspace'));

        $profile = $user->profile()->firstOrFail();
        $this->assertContains('customer_owner', $profile->metadata['capabilities']);
        $this->assertContains('marketer_partner', $profile->metadata['capabilities']);
        $this->assertContains('implementation_builder', $profile->metadata['capabilities']);
        $this->assertSame('I want to help customers build their apps.', $profile->metadata['capability_note']);
    }

    public function test_v16_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V16CustomerExperience::validationReport();

        $this->assertSame('V16', $report['version']);
        $this->assertNotContains(false, $report['routes']);
        $this->assertNotContains(false, $report['config']);
        $this->assertNotContains(false, $report['files']);
        $this->assertNotContains(false, $report['docs']);
        $this->assertTrue($report['tests']);
        $this->assertTrue(V16CustomerExperience::isReleaseCandidateReady());
    }
}
