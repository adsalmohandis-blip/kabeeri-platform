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
            ->assertSee('data-language-context="platform_public"', false)
            ->assertSee('href="http://localhost/en"', false)
            ->assertSee(__('kabeeri.language.label'))
            ->assertSee('English')
            ->assertSee('href="http://localhost/es"', false)
            ->assertSee('href="http://localhost/ru"', false)
            ->assertSee('href="http://localhost/hi"', false)
            ->assertSee('href="http://localhost/ur"', false);
    }

    public function test_language_switch_persists_locale_and_redirects_back_to_requested_surface(): void
    {
        $this->get(route('language.switch', ['locale' => 'en', 'redirect' => '/start']))
            ->assertRedirect('/en/start')
            ->assertSessionHas('kabeeri_locale_platform_public', 'en')
            ->assertSessionHas('kabeeri_locale', 'en');

        $this->withSession(['kabeeri_locale_platform_public' => 'en'])
            ->get('/start')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('data-language-context="platform_public"', false)
            ->assertSee('Language')
            ->assertDontSee('اللغة');
    }

    public function test_customer_dashboard_has_its_own_language_switcher_context(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['kabeeri_locale_customer' => 'en'])
            ->get(route('customer.workspace'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('data-language-context="customer"', false);
    }

    public function test_platform_admin_language_and_font_live_inside_user_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['kabeeri_locale_admin' => 'en'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('User settings')
            ->assertDontSee('data-language-context="admin"', false)
            ->assertDontSee('data-font-context="admin"', false);

        $this->actingAs($user)
            ->withSession(['kabeeri_locale_admin' => 'en'])
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('User settings')
            ->assertSee('Admin language')
            ->assertSee('Admin font')
            ->assertSee('Clear Arabic font');
    }

    public function test_platform_admin_user_settings_preferences_are_loaded_from_profile(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'visibility' => 'private',
            'metadata' => [
                'admin_locale' => 'en',
                'admin_font' => 'tajawal',
            ],
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Platform Admin Dashboard')
            ->assertSee('--kbr-admin-font-family: "Tajawal", "IBM Plex Sans Arabic", sans-serif;', false);
    }

    public function test_new_common_languages_are_supported(): void
    {
        foreach (['es', 'ru', 'hi', 'ur', 'it', 'fr', 'de', 'pt', 'tr', 'id', 'zh', 'ja', 'ko', 'bn'] as $locale) {
            $this->get(route('language.switch', ['locale' => $locale, 'redirect' => '/']))
                ->assertRedirect('/'.$locale)
                ->assertSessionHas('kabeeri_locale_platform_public', $locale)
                ->assertSessionHas('kabeeri_locale', $locale);
        }
    }

    public function test_localized_slug_routes_set_the_right_context_locale(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSessionHas('kabeeri_locale_platform_public', 'en');

        $this->get('/ar/start')
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSessionHas('kabeeri_locale_platform_public', 'ar');
    }

    public function test_theme_and_font_preferences_are_saved_per_interface_context(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('ui.theme', ['theme' => 'dark', 'redirect' => '/customer/dashboard']))
            ->assertRedirect('/customer/dashboard')
            ->assertSessionHas('kabeeri_theme_customer', 'dark');

        $this->actingAs($user)
            ->get(route('ui.font', ['font' => 'tajawal', 'redirect' => '/customer/dashboard']))
            ->assertRedirect('/customer/dashboard')
            ->assertSessionHas('kabeeri_font_customer', 'tajawal');

        $this->get(route('ui.font', ['font' => 'tajawal', 'redirect' => '/']))
            ->assertForbidden();
    }

    public function test_arabic_and_english_entry_pages_do_not_mix_core_copy(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('كبيري')
            ->assertSee('ابدأ الآن')
            ->assertDontSee('Start now')
            ->assertDontSee('Language')
            ->assertDontSee('KABEERI');

        $this->withSession(['kabeeri_locale_platform_public' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('KABEERI')
            ->assertSee('Start now')
            ->assertDontSee('ابدأ الآن')
            ->assertDontSee('اللغة')
            ->assertDontSee('كبيري');
    }

    public function test_customer_start_page_keeps_visible_copy_in_selected_language(): void
    {
        $this->get('/start')
            ->assertOk()
            ->assertSeeText('صاحب عمل')
            ->assertDontSeeText('Business owner')
            ->assertDontSeeText('Start now')
            ->assertDontSeeText('Language')
            ->assertDontSeeText('KABEERI');

        $this->withSession(['kabeeri_locale_platform_public' => 'en'])
            ->get('/start')
            ->assertOk()
            ->assertSeeText('Business owner')
            ->assertDontSeeText('صاحب عمل')
            ->assertDontSeeText('ابدأ الآن')
            ->assertDontSeeText('اللغة')
            ->assertDontSeeText('كبيري');
    }

    public function test_unsupported_language_is_rejected(): void
    {
        $this->get('/language/xx')->assertNotFound();
    }
}
