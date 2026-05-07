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
