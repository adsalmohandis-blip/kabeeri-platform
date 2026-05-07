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
            ->assertSee('KABEERI Business Client')
            ->assertSee('ابدأ مسار العميل')
            ->assertSee('Command Center انتقل لمسار خاص')
            ->assertDontSee('Task Tracker Truth')
            ->assertDontSee('V15 Public Web Manifest');
    }

    public function test_command_center_is_private_and_renders_live_system_data_sections(): void
    {
        $this->seed(FreemiumSeeder::class);

        $this->get(route('system.command-center'))
            ->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get(route('system.command-center'))
            ->assertOk()
            ->assertSee('KABEERI Command Center')
            ->assertSee('Task Tracker Truth')
            ->assertSee('Release Candidate')
            ->assertSee('V15 Public Web Manifest')
            ->assertSee('Next.js Public Runtime')
            ->assertSee('Database')
            ->assertSee('Modules')
            ->assertSee('Freemium')
            ->assertSee('Entitlements')
            ->assertSee('V9')
            ->assertSee('V15')
            ->assertSee('FREEMIUM')
            ->assertSee('Free / Community');
    }
}
