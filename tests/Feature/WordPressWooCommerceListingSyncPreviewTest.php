<?php

namespace Tests\Feature;

use App\Models\MallSyncSource;
use App\Modules\Mall\Services\WordPressWooCommerceListingSyncPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WordPressWooCommerceListingSyncPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_wordpress_listing_payload_can_be_previewed_without_syncing(): void
    {
        $source = MallSyncSource::factory()->create([
            'source_type' => 'wordpress',
            'sync_scope' => 'business_directory',
            'status' => 'draft',
        ]);

        $event = app(WordPressWooCommerceListingSyncPreview::class)->preview($source, [
            ['type' => 'business', 'title' => 'About Us', 'slug' => 'about-us'],
            ['slug' => 'missing-title'],
        ]);

        $this->assertSame('preview', $event->event_type);
        $this->assertSame('completed', $event->status);
        $this->assertSame(2, $event->total_records);
        $this->assertSame(1, $event->processed_records);
        $this->assertSame(['Row 2 is missing a title or name.'], $event->warnings);
        $this->assertSame('business_directory', $event->metadata['sync_scope']);
        $this->assertNotNull($source->refresh()->last_preview_at);
        $this->assertNull($source->last_synced_at);
    }

    public function test_woocommerce_product_payload_can_be_previewed(): void
    {
        $source = MallSyncSource::factory()->create([
            'source_type' => 'woocommerce',
            'sync_scope' => 'products',
        ]);

        $event = app(WordPressWooCommerceListingSyncPreview::class)->preview($source, [
            'items' => [
                ['type' => 'product', 'name' => 'Woo Product', 'slug' => 'woo-product'],
                ['type' => 'unknown', 'name' => 'Odd Product', 'slug' => 'odd-product'],
                ['type' => 'product', 'name' => 'Missing Slug'],
            ],
        ]);

        $this->assertSame(1, $event->processed_records);
        $this->assertSame(2, $event->failed_records);
        $this->assertSame([
            'Row 2 has an unsupported listing type.',
            'Row 3 is missing a slug.',
        ], $event->warnings);
    }

    public function test_preview_parse_rejects_non_array_items(): void
    {
        $result = app(WordPressWooCommerceListingSyncPreview::class)->parse(['items' => 'not-array']);

        $this->assertSame([], $result['rows']);
        $this->assertSame(['Payload items must be an array.'], $result['warnings']);
    }

    public function test_preview_rejects_non_wordpress_source_type(): void
    {
        $source = MallSyncSource::factory()->create(['source_type' => 'csv_feed']);

        $this->expectException(ValidationException::class);

        app(WordPressWooCommerceListingSyncPreview::class)->preview($source, [
            ['title' => 'CSV Row'],
        ]);
    }
}
