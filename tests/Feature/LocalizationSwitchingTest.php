<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationSwitchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_pages_have_accessible_language_switcher_and_default_rtl_locale(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('data-language-context="visitor"', false)
            ->assertSee('/language/en', false)
            ->assertSee('اللغة');
    }

    public function test_language_switch_persists_locale_and_redirects_back_to_requested_surface(): void
    {
        $this->get(route('language.switch', ['locale' => 'en', 'redirect' => '/start']))
            ->assertRedirect('/start')
            ->assertSessionHas('kabeeri_locale', 'en');

        $this->withSession(['kabeeri_locale' => 'en'])
            ->get('/start')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('data-language-context="visitor"', false)
            ->assertSee('Language');
    }

    public function test_customer_dashboard_has_its_own_language_switcher_context(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['kabeeri_locale' => 'en'])
            ->get(route('customer.workspace'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('data-language-context="customer"', false);
    }

    public function test_platform_admin_dashboard_has_team_language_switcher_context(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['kabeeri_locale' => 'en'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('data-language-context="admin"', false)
            ->assertSee('Language');
    }

    public function test_unsupported_language_is_rejected(): void
    {
        $this->get('/language/fr')->assertNotFound();
    }
}
