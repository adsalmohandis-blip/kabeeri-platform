<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\CMS\Services\SeoService;
use App\Modules\Core\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsSeoFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_fields_are_stored_and_read_correctly(): void
    {
        $entry = ContentEntry::factory()->create();
        $user = User::factory()->create();
        $media = app(MediaService::class)->createAsset([
            'organization_id' => $entry->organization_id,
            'site_id' => $entry->site_id,
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => 'organizations/'.$entry->organization_id.'/media/og.png',
            'relative_path' => 'media/og.png',
            'filename' => 'og.png',
            'original_filename' => 'og.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size_bytes' => 1024,
            'visibility' => 'public',
        ]);

        $updated = app(SeoService::class)->write($entry, [
            'title' => 'SEO title',
            'description' => 'SEO description',
            'canonical_url' => 'https://example.test/pages/seo-title',
            'og_title' => 'Open graph title',
            'og_description' => 'Open graph description',
            'og_image_media_id' => $media->id,
            'noindex' => true,
        ]);

        $seo = app(SeoService::class)->read($updated);

        $this->assertSame('SEO title', $seo->title);
        $this->assertSame('SEO description', $seo->description);
        $this->assertSame('https://example.test/pages/seo-title', $seo->canonicalUrl);
        $this->assertSame('Open graph title', $seo->ogTitle);
        $this->assertSame('Open graph description', $seo->ogDescription);
        $this->assertSame($media->id, $seo->ogImageMediaId);
        $this->assertTrue($seo->noindex);
        $this->assertTrue($updated->ogImage()->whereKey($media->id)->exists());
    }

    public function test_seo_reader_keeps_legacy_json_compatible(): void
    {
        $entry = ContentEntry::factory()->create([
            'seo' => [
                'title' => 'Legacy title',
                'description' => 'Legacy description',
                'canonical_url' => 'https://example.test/legacy',
                'noindex' => true,
            ],
        ]);

        $seo = app(SeoService::class)->read($entry);

        $this->assertSame('Legacy title', $seo->title);
        $this->assertSame('Legacy description', $seo->description);
        $this->assertSame('https://example.test/legacy', $seo->canonicalUrl);
        $this->assertTrue($seo->noindex);
    }
}
