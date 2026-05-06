<?php

namespace Tests\Feature;

use App\Models\Theme;
use App\Models\User;
use App\Modules\Core\Services\InternalMarketplaceCatalogService;
use App\Modules\Core\Services\ThemeStoreCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeStoreCatalogFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_store_lists_approved_internal_theme_items_with_filters(): void
    {
        $theme = $this->createTheme('Legal Clean', 'legal-clean', [
            'category' => 'professional',
            'industries' => ['legal'],
            'app_types' => ['website'],
            'price_type' => 'free',
            'performance_score' => 92,
        ]);
        $otherTheme = $this->createTheme('Retail Blocks', 'retail-blocks', [
            'category' => 'commerce',
            'industries' => ['retail'],
            'app_types' => ['store'],
            'price_type' => 'free',
            'performance_score' => 88,
        ]);

        $marketplace = app(InternalMarketplaceCatalogService::class);
        $item = $marketplace->approve($marketplace->register($theme, ['is_featured' => true]), User::factory()->create());
        $marketplace->approve($marketplace->register($otherTheme), User::factory()->create());

        $items = app(ThemeStoreCatalogService::class)->list([
            'industry' => 'legal',
            'app_type' => 'website',
            'category' => 'professional',
            'price_type' => 'free',
            'min_performance' => 90,
            'featured' => true,
        ]);

        $this->assertCount(1, $items);
        $this->assertSame($item->id, $items->first()?->id);
        $this->assertTrue($items->first()?->catalogable->is($theme));
    }

    public function test_theme_store_hides_unapproved_theme_items(): void
    {
        $theme = $this->createTheme('Draft Theme', 'draft-theme');

        app(InternalMarketplaceCatalogService::class)->register($theme);

        $items = app(ThemeStoreCatalogService::class)->list();

        $this->assertCount(0, $items);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createTheme(string $name, string $slug, array $attributes = []): Theme
    {
        return Theme::query()->create([
            ...$attributes,
            'name' => $name,
            'slug' => $slug,
            'type' => 'official',
            'status' => 'active',
        ]);
    }
}
