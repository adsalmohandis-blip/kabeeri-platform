<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapAndRobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_includes_published_public_entries_only(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'demo-app',
        ]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'slug' => 'page',
        ]);

        $published = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'published-page',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $draft = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'draft-page',
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        $private = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'private-page',
            'status' => 'published',
            'visibility' => 'private',
        ]);
        $noindex = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'noindex-page',
            'status' => 'published',
            'visibility' => 'public',
            'noindex' => true,
        ]);

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');

        $response->assertSee('/app/'.$site->slug.'/'.$published->slug, false);
        $response->assertDontSee('/app/'.$site->slug.'/'.$draft->slug, false);
        $response->assertDontSee('/app/'.$site->slug.'/'.$private->slug, false);
        $response->assertDontSee('/app/'.$site->slug.'/'.$noindex->slug, false);
    }

    public function test_robots_output_points_to_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('User-agent: *')
            ->assertSee('Allow: /')
            ->assertSee('/sitemap.xml');
    }
}
