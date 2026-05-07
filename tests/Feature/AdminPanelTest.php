<?php

namespace Tests\Feature;

use App\Http\Middleware\ResetCustomerSessionForAdminLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_admin_home_uses_compact_localized_dashboard_widget(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('لوحة أدمن المنصة')
            ->assertSee('لوحة حالة التطوير')
            ->assertSee('الأشخاص')
            ->assertSee('مشروعات الأعمال')
            ->assertSee('data-admin-icon-scale="compact"', false)
            ->assertDontSee('fi-filament-info-widget-logo')
            ->assertDontSee('fi-account-widget')
            ->assertDontSee('People')
            ->assertDontSee('business projects')
            ->assertDontSee('Welcome to');

        $this->actingAs(User::factory()->create())
            ->withSession(['kabeeri_locale_admin' => 'en'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('Platform Admin Dashboard')
            ->assertSee('Development status')
            ->assertSee('Open development status')
            ->assertDontSee('لوحة حالة التطوير');
    }

    public function test_customer_session_can_open_platform_admin_login_without_loop(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession([ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE => 'customer'])
            ->get('/admin/login')
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertGuest();

        $this->get('/admin/login')->assertOk();
    }

    public function test_localized_admin_login_resets_customer_session_without_loop(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession([ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE => 'customer'])
            ->get('/en/admin/login')
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertGuest();

        $this->get('/admin/login')->assertOk();
    }

    public function test_platform_admin_login_entry_resets_customer_session(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession([ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE => 'customer'])
            ->get(route('admin.login.entry'))
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertGuest();
    }
}
