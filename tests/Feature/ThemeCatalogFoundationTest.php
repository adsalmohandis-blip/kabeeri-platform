<?php

namespace Tests\Feature;

use App\Models\Theme;
use App\Modules\Core\Services\ThemeCatalogService;
use Database\Seeders\ThemesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeCatalogFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_catalog_seed_contains_official_samples(): void
    {
        $this->seed(ThemesSeeder::class);
        $this->seed(ThemesSeeder::class);

        $this->assertSame(2, Theme::query()->count());
        $this->assertDatabaseHas('themes', [
            'slug' => 'kabeeri-starter',
            'price_type' => 'free',
            'category' => 'starter',
        ]);
        $this->assertDatabaseHas('themes', [
            'slug' => 'kabeeri-commerce-lite',
            'price_type' => 'free',
            'category' => 'commerce',
        ]);
    }

    public function test_themes_can_be_filtered_by_industry_app_type_and_price_type(): void
    {
        $this->seed(ThemesSeeder::class);

        $themes = app(ThemeCatalogService::class)->list([
            'industry' => 'retail',
            'app_type' => 'store',
            'price_type' => 'free',
        ]);

        $this->assertSame(['kabeeri-commerce-lite'], $themes->pluck('slug')->all());
    }
}
