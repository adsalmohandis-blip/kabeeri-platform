<?php

namespace Tests\Feature;

use App\Models\MallSyncSource;
use App\Modules\Mall\Services\CsvFeedPublicSyncProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvFeedPublicSyncProcessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_feed_processor_parses_rows_and_warnings(): void
    {
        $result = app(CsvFeedPublicSyncProcessor::class)->parse(
            "title,slug,price\nProduct A,product-a,10\nBroken Row\n",
        );

        $this->assertCount(1, $result['rows']);
        $this->assertSame('Product A', $result['rows'][0]['title']);
        $this->assertSame(['Row 3 column count does not match header.'], $result['warnings']);
    }

    public function test_csv_feed_preview_creates_sync_event_without_publishing(): void
    {
        $source = MallSyncSource::factory()->create([
            'source_type' => 'csv_feed',
            'sync_scope' => 'products',
            'status' => 'draft',
        ]);

        $event = app(CsvFeedPublicSyncProcessor::class)->preview(
            $source,
            "title,slug,price\nProduct A,product-a,10\n",
        );

        $this->assertSame('preview', $event->event_type);
        $this->assertSame('completed', $event->status);
        $this->assertSame(1, $event->processed_records);
        $this->assertSame(0, $event->failed_records);
        $this->assertSame('draft', $source->refresh()->status);
        $this->assertNotNull($source->last_preview_at);
        $this->assertNull($source->last_synced_at);
    }

    public function test_empty_csv_feed_records_warning(): void
    {
        $event = app(CsvFeedPublicSyncProcessor::class)->preview(MallSyncSource::factory()->create(), '');

        $this->assertSame(['CSV feed is empty.'], $event->warnings);
        $this->assertSame(0, $event->processed_records);
        $this->assertSame(1, $event->failed_records);
    }
}
