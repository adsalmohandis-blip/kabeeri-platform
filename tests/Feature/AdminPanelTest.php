<?php

namespace Tests\Feature;

use App\Filament\Pages\UserSettings;
use App\Http\Middleware\ResetCustomerSessionForAdminLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            ->assertSee('إعدادات المستخدم')
            ->assertSee('data-admin-icon-scale="compact"', false)
            ->assertDontSee('fi-filament-info-widget-logo')
            ->assertDontSee('fi-account-widget')
            ->assertDontSee('data-language-context="admin"', false)
            ->assertDontSee('data-font-context="admin"', false)
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

    public function test_admin_user_settings_host_language_and_font_controls(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('إعدادات المستخدم')
            ->assertSee('لغة لوحة الأدمن')
            ->assertSee('مظهر لوحة الأدمن')
            ->assertSee('خط لوحة الأدمن')
            ->assertSee('داكن')
            ->assertSee('المراعي')
            ->assertDontSee('Admin language')
            ->assertDontSee('Admin theme')
            ->assertDontSee('Admin font');
    }

    public function test_admin_user_settings_save_language_and_font_preferences(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
        ]);

        $this->actingAs($user);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'name' => 'Admin User',
                'email' => 'admin@example.test',
                'admin_locale' => 'en',
                'admin_theme' => 'dark',
                'admin_font' => 'tajawal',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('en', session('kabeeri_locale_admin'));
        $this->assertSame('dark', session('kabeeri_theme_admin'));
        $this->assertSame('tajawal', session('kabeeri_font_admin'));
        $this->assertSame('en', $user->profile()->firstOrFail()->metadata['admin_locale']);
        $this->assertSame('dark', $user->profile()->firstOrFail()->metadata['admin_theme']);
        $this->assertSame('tajawal', $user->profile()->firstOrFail()->metadata['admin_font']);
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
