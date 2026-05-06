<?php

namespace Tests\Feature;

use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RootDashboardPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_dashboard_renders_live_system_data_sections(): void
    {
        $this->seed(FreemiumSeeder::class);

        $this->get('/')
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
