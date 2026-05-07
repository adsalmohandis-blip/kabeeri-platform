<?php

namespace Tests\Feature;

use App\Http\Middleware\ResetCustomerSessionForAdminLogin;
use App\Models\ContentEntry;
use App\Models\InstalledPackage;
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
            ->assertSee(__('kabeeri.brand.name'))
            ->assertSee(__('kabeeri.ui.start_now'))
            ->assertDontSee(__('kabeeri.brand.name').' Customer Start')
            ->assertSee(__('kabeeri.customer.paths.business_owner.label'))
            ->assertSee(__('kabeeri.customer.paths.needs_builder.label'))
            ->assertSee(__('kabeeri.customer.themes.kabeeri-atlas.name'))
            ->assertSee('quick-register')
            ->assertSee('quick_name')
            ->assertSee('quick_password_confirmation');
    }

    public function test_register_page_prioritizes_visible_create_account_form(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee(__('kabeeri.ui.start_now'))
            ->assertDontSee('Create account form')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password_confirmation"', false);
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
        $this->assertSame('acme-store', $site->username);
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
            ->assertSee(__('kabeeri.ui.apps_dashboard'))
            ->assertDontSee('Client Workspace')
            ->assertSee('workspace-sidebar')
            ->assertSee('class="kbr-customer-shell grid min-h-screen w-full lg:grid-cols-[16rem_minmax(0,1fr)]"', false)
            ->assertDontSee('max-w-[1440px]', false)
            ->assertSee(__('kabeeri.ui.apps'))
            ->assertSee('Acme Store')
            ->assertSee(__('kabeeri.customer.themes.mall-window.name'))
            ->assertSee('/customer/apps/acme-store', false)
            ->assertDontSee('/customer/apps/'.$site->id, false);

        $this->assertSame(
            url('/customer/apps/acme-store'),
            route('customer.apps.show', ['username' => $site->username]),
        );

        $this->get(route('customer.apps.show', ['username' => $site->username]))
            ->assertOk()
            ->assertSee(__('kabeeri.ui.app_active'))
            ->assertSee(__('kabeeri.customer.themes.mall-window.name'))
            ->assertSee(__('kabeeri.ui.username'))
            ->assertSee('acme-store');

        $this->get('/customer/apps/'.$site->id)
            ->assertNotFound();
    }

    public function test_customer_dashboard_is_protected_and_foreign_app_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $this->get(route('customer.workspace'))->assertRedirect('/login');

        $this->actingAs($other)
            ->get(route('customer.apps.show', ['username' => $site->username]))
            ->assertForbidden();
    }

    public function test_authenticated_customer_entry_redirects_to_workspace_not_public_home(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('customer.workspace'));
    }

    public function test_authenticated_customer_auth_pages_redirect_to_workspace_not_home(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/login')
            ->assertRedirect(route('customer.workspace'));

        $this->actingAs($customer)
            ->get('/register')
            ->assertRedirect(route('customer.workspace'));
    }

    public function test_customer_app_routes_preserve_intended_destination_after_login(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@example.test',
            'password' => 'password-123',
        ]);
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Owner App',
            'slug' => 'owner-app',
        ]);

        $this->get(route('customer.apps.show', ['username' => $site->username]))
            ->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'owner@example.test',
            'password' => 'password-123',
        ])
            ->assertRedirect(route('customer.apps.show', ['username' => $site->username]))
            ->assertSessionHas(ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE, 'customer');

        $this->assertAuthenticatedAs($owner);

        $this->get('/en/customer/apps/'.$site->username)
            ->assertOk()
            ->assertSessionHas('kabeeri_locale_customer', 'en');
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

    public function test_customer_can_manage_apps_themes_plugins_and_trash_by_username(): void
    {
        $user = User::factory()->create(['password' => 'password-123']);
        $this->actingAs($user);

        $this->post(route('customer.onboarding.store'), [
            'customer_path' => 'business_owner',
            'organization_name' => 'Owner Workspace',
            'company_name' => 'Owner Company',
            'site_name' => 'Owner Main',
            'app_type' => 'website',
            'theme_slug' => 'kabeeri-atlas',
        ])->assertRedirect(route('customer.workspace'));

        $this->get(route('customer.apps.index'))
            ->assertOk()
            ->assertSee(__('kabeeri.ui.apps_manage'))
            ->assertSee('Owner Main');

        $this->get(route('customer.apps.create'))
            ->assertOk()
            ->assertSee(__('kabeeri.ui.create_app'))
            ->assertSee('name="public_language"', false)
            ->assertSee('name="public_theme_mode"', false)
            ->assertSee('name="public_font"', false);

        $this->post(route('customer.apps.store'), [
            'site_name' => 'Second App',
            'username' => 'second-app',
            'app_type' => 'website',
            'theme_slug' => 'kabeeri-atlas',
            'public_language' => 'en',
            'public_theme_mode' => 'dark',
            'public_font' => 'tajawal',
        ])->assertRedirect(route('customer.apps.show', ['username' => 'second-app']));

        $site = Site::query()->where('slug', 'second-app')->firstOrFail();
        $this->assertSame('second-app', $site->username);
        $this->assertSame('en', $site->language);
        $this->assertSame('dark', $site->metadata['public_theme_mode']);
        $this->assertSame('tajawal', $site->metadata['public_font']);

        $this->put(route('customer.apps.update', ['username' => 'second-app']), [
            'site_name' => 'Second App Pro',
            'username' => 'second-app-pro',
            'app_type' => 'website',
            'status' => 'paused',
            'public_language' => 'fr',
            'public_theme_mode' => 'light',
            'public_font' => 'almarai',
        ])->assertRedirect(route('customer.apps.show', ['username' => 'second-app-pro']));

        $site = $site->refresh();
        $this->assertSame('second-app-pro', $site->username);
        $this->assertSame('paused', $site->status);
        $this->assertSame('fr', $site->language);
        $this->assertSame('almarai', $site->metadata['public_font']);

        $this->get(route('customer.apps.themes', ['username' => $site->username]))
            ->assertOk()
            ->assertSee(__('kabeeri.ui.switch_theme'));

        $this->patch(route('customer.apps.themes.update', ['username' => $site->username]), [
            'theme_slug' => 'launch-loom',
        ])->assertRedirect(route('customer.apps.themes', ['username' => $site->username]));

        $this->assertSame('launch-loom', $site->refresh()->theme?->slug);

        $this->get(route('customer.apps.plugins', ['username' => $site->username]))
            ->assertOk()
            ->assertSee(__('kabeeri.ui.plugins'));

        $this->post(route('customer.apps.plugins.install', ['username' => $site->username, 'package' => 'commerce-pulse-pack']))
            ->assertRedirect(route('customer.apps.plugins', ['username' => $site->username]));

        $installed = InstalledPackage::query()->where('site_id', $site->id)->firstOrFail();
        $this->assertSame('active', $installed->status);
        $this->assertSame('commerce-pulse-pack', $installed->package->slug);

        $this->patch(route('customer.apps.plugins.deactivate', ['username' => $site->username, 'package' => 'commerce-pulse-pack']))
            ->assertRedirect(route('customer.apps.plugins', ['username' => $site->username]));
        $this->assertSame('inactive', $installed->refresh()->status);

        $this->patch(route('customer.apps.plugins.activate', ['username' => $site->username, 'package' => 'commerce-pulse-pack']))
            ->assertRedirect(route('customer.apps.plugins', ['username' => $site->username]));
        $this->assertSame('active', $installed->refresh()->status);

        $this->delete(route('customer.apps.destroy', ['username' => $site->username]), [
            'retention_days' => 60,
        ])->assertRedirect(route('customer.apps.trash'));

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
        $trashedSite = Site::withTrashed()->findOrFail($site->id);
        $this->assertSame(60, $trashedSite->metadata['trash_retention_days']);

        $this->get(route('customer.apps.trash'))
            ->assertOk()
            ->assertSee('Second App Pro');

        $this->patch(route('customer.apps.schedule-delete', ['username' => $site->username]), [
            'retention_days' => 90,
        ])->assertRedirect(route('customer.apps.trash'));
        $trashedSite = Site::withTrashed()->findOrFail($site->id);
        $this->assertSame(90, $trashedSite->metadata['trash_retention_days']);

        $this->post(route('customer.apps.restore', ['username' => $site->username]))
            ->assertRedirect(route('customer.apps.index'));
        $restoredSite = Site::query()->findOrFail($site->id);
        $this->assertFalse($restoredSite->trashed());
        $this->assertSame('active', $restoredSite->status);
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
