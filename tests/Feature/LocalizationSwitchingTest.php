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
            ->assertSee(__('kabeeri.language.label'))
            ->assertSee(__('kabeeri.language.names.en'))
            ->assertSee('/language/es', false)
            ->assertSee('/language/ru', false)
            ->assertSee('/language/hi', false)
            ->assertSee('/language/ur', false);
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
            ->assertSee('Language')
            ->assertDontSee('اللغة');
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

    public function test_new_common_languages_are_supported(): void
    {
        foreach (['es', 'ru', 'hi', 'ur', 'it', 'fr', 'de', 'pt', 'tr', 'id', 'zh', 'ja', 'ko', 'bn'] as $locale) {
            $this->get(route('language.switch', ['locale' => $locale, 'redirect' => '/']))
                ->assertRedirect('/')
                ->assertSessionHas('kabeeri_locale', $locale);
        }
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

        $this->withSession(['kabeeri_locale' => 'en'])
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

        $this->withSession(['kabeeri_locale' => 'en'])
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
