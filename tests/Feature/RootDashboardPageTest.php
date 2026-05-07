<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RootDashboardPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_root_renders_business_client_entry_instead_of_system_dashboard(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(__('kabeeri.brand.name'))
            ->assertSee(__('kabeeri.ui.start_now'))
            ->assertDontSee(__('kabeeri.brand.name').' Business Client')
            ->assertDontSee('ابدأ مسار العميل')
            ->assertDontSee('Command Center انتقل لمسار خاص')
            ->assertDontSee('Internal Command Center')
            ->assertDontSee('Task Tracker Truth')
            ->assertDontSee('V15 Public Web Manifest');
    }

    public function test_command_center_is_private_and_redirects_to_admin_development_status(): void
    {
        $this->seed(FreemiumSeeder::class);
        $user = User::factory()->create();

        $this->get(route('system.command-center'))
            ->assertRedirect('/login');

        $this->actingAs($user)
            ->get(route('system.command-center'))
            ->assertRedirect(route('filament.admin.pages.development-status'));

        $this->actingAs($user)
            ->withSession(['kabeeri_locale' => 'en'])
            ->get(route('filament.admin.pages.development-status'))
            ->assertOk()
            ->assertSee('Development status')
            ->assertSee('Admin shortcuts')
            ->assertSee('Task tracker')
            ->assertSee('Database status');
    }

    public function test_main_admin_dashboard_contains_development_status_entry(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['kabeeri_locale' => 'en'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('Development status')
            ->assertSee('Open development status');
    }
}
