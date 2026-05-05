<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Modules\CMS\Services\WordPressSeoMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressSeoMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_yoast_seo_metadata_maps_to_content_seo_fields(): void
    {
        $entry = ContentEntry::factory()->create();

        $result = app(WordPressSeoMapper::class)->apply($entry, [
            '_yoast_wpseo_title' => 'Yoast Title',
            '_yoast_wpseo_metadesc' => 'Yoast description',
            '_yoast_wpseo_canonical' => 'https://example.test/canonical',
            '_yoast_wpseo_meta-robots-noindex' => '1',
            '_yoast_wpseo_opengraph-title' => 'OG Title',
            '_yoast_wpseo_opengraph-description' => 'OG description',
            '_yoast_wpseo_focuskw' => 'unsupported',
        ], overwrite: true);

        $entry = $result['entry'];
        $this->assertSame('Yoast Title', $entry->seo_title);
        $this->assertSame('Yoast description', $entry->seo_description);
        $this->assertSame('https://example.test/canonical', $entry->canonical_url);
        $this->assertTrue($entry->noindex);
        $this->assertSame('OG Title', $entry->og_title);
        $this->assertSame('OG description', $entry->og_description);
        $this->assertSame('Unsupported SEO metadata key [_yoast_wpseo_focuskw].', $result['warnings'][0]);
    }

    public function test_rank_math_metadata_maps_without_overwriting_existing_seo_by_default(): void
    {
        $entry = ContentEntry::factory()->create([
            'seo_title' => 'Manual title',
        ]);

        $result = app(WordPressSeoMapper::class)->apply($entry, [
            'rank_math_title' => 'RankMath Title',
            'rank_math_description' => 'RankMath description',
            'rank_math_robots' => 'index,follow',
        ]);

        $this->assertTrue($result['skipped']);
        $this->assertSame('Manual title', $entry->refresh()->seo_title);
        $this->assertContains('Existing SEO was not overwritten.', $result['warnings']);
    }
}
