<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_public_page_renders_for_public_route(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'main-app',
            'name' => 'Main App',
        ]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'title' => 'Welcome Page',
            'slug' => 'welcome-page',
            'body' => 'Public content body',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        $this->get('/app/'.$site->slug.'/'.$entry->slug)
            ->assertOk()
            ->assertSee('Welcome Page')
            ->assertSee('Public content body');
    }

    public function test_draft_page_does_not_render_publicly(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'main-app',
        ]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'draft-page',
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        $this->get('/app/'.$site->slug.'/'.$entry->slug)
            ->assertNotFound();
    }

    public function test_public_page_uses_rtl_direction_for_arabic_site_language(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'arabic-app',
            'language' => 'ar',
        ]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'title' => 'مرحبا',
            'slug' => 'marhaba',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        $this->get('/app/'.$site->slug.'/'.$entry->slug)
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }
}
