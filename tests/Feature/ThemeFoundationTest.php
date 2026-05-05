<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\Theme;
use App\Models\ThemeSetting;
use App\Modules\Core\Services\ThemeRegistryService;
use Database\Seeders\ThemesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_theme_is_seeded(): void
    {
        $this->seed(ThemesSeeder::class);

        $this->assertDatabaseHas('themes', [
            'slug' => 'kabeeri-starter',
            'type' => 'official',
            'status' => 'active',
        ]);
    }

    public function test_site_can_reference_theme_and_store_theme_settings(): void
    {
        $this->seed(ThemesSeeder::class);

        $site = Site::factory()->create();
        $theme = Theme::query()->where('slug', 'kabeeri-starter')->firstOrFail();
        $service = app(ThemeRegistryService::class);

        $service->assignToSite($site, $theme);
        $setting = $service->setSiteSetting($site, $theme, 'layout.home.hero_style', 'minimal');

        $this->assertSame($theme->id, $site->fresh()->theme_id);
        $this->assertDatabaseHas('theme_settings', [
            'id' => $setting->id,
            'site_id' => $site->id,
            'theme_id' => $theme->id,
            'key' => 'layout.home.hero_style',
        ]);

        $this->assertSame(
            'minimal',
            $service->getSiteSetting($site, $theme, 'layout.home.hero_style'),
        );

        $this->assertCount(1, ThemeSetting::query()->where('site_id', $site->id)->get());
    }
}
