<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\Site;
use App\Models\ThemeAppRecipe;
use App\Modules\Core\Services\DemoImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeDemoImporterBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_pages_can_be_generated_for_site(): void
    {
        $site = Site::factory()->create();
        $recipe = ThemeAppRecipe::factory()->create(['app_type' => 'website']);

        $result = app(DemoImportService::class)->importForSite($recipe, $site);

        $this->assertCount(2, $result['created']);
        $this->assertDatabaseHas('content_entries', [
            'site_id' => $site->id,
            'slug' => 'home',
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'theme_demo.imported_page',
            'site_id' => $site->id,
        ]);
    }

    public function test_existing_content_is_not_overwritten_by_default(): void
    {
        $site = Site::factory()->create();
        $recipe = ThemeAppRecipe::factory()->create();
        ContentEntry::factory()->create([
            'organization_id' => $site->organization_id,
            'site_id' => $site->id,
            'slug' => 'home',
            'title' => 'Existing Home',
        ]);

        $result = app(DemoImportService::class)->importForSite($recipe, $site);

        $this->assertContains('home', $result['skipped']);
        $this->assertSame('Existing Home', ContentEntry::query()->where('site_id', $site->id)->where('slug', 'home')->first()?->title);
    }
}
