<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalConciseCopyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_facing_core_copy_stays_short_in_arabic_and_english(): void
    {
        $keys = [
            'kabeeri.ui.hero_title',
            'kabeeri.ui.hero_lead',
            'kabeeri.ui.start_title',
            'kabeeri.ui.start_lead',
            'kabeeri.ui.open_account_title',
            'kabeeri.ui.after_login_text',
            'kabeeri.ui.onboarding_title',
            'kabeeri.ui.onboarding_lead',
            'kabeeri.ui.first_app',
            'kabeeri.customer.paths.business_owner.headline',
            'kabeeri.customer.paths.needs_builder.headline',
            'kabeeri.customer.dashboard_cards.app.text',
            'kabeeri.customer.dashboard_cards.marketplace.text',
        ];

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);

            foreach ($keys as $key) {
                $this->assertWordsAtMost(__($key), 8, "{$locale}: {$key}");
            }
        }
    }

    public function test_external_visitor_and_customer_pages_do_not_render_long_paragraphs(): void
    {
        foreach ([
            route('public.landing'),
            route('marketplace.home'),
            route('mall.search'),
            route('customer.start'),
        ] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            preg_match_all('/<p\b[^>]*>(.*?)<\/p>/isu', $html, $matches);

            foreach ($matches[1] as $index => $paragraph) {
                $text = trim(html_entity_decode(strip_tags($paragraph), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if ($text === '') {
                    continue;
                }

                $this->assertWordsAtMost($text, 24, "{$url} paragraph {$index}");
            }
        }
    }

    private function assertWordsAtMost(string $text, int $max, string $label): void
    {
        $words = preg_split('/[\s،,.;:\/]+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        $this->assertLessThanOrEqual(
            $max,
            count($words ?: []),
            "{$label} is too long: {$text}",
        );
    }
}
