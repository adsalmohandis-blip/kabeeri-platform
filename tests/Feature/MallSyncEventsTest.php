<?php

namespace Tests\Feature;

use App\Models\MallSyncSource;
use App\Modules\Mall\Services\MallSyncEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallSyncEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_event_can_record_preview_summary_and_update_source(): void
    {
        $source = MallSyncSource::factory()->create();
        $service = app(MallSyncEventService::class);

        $event = $service->start($source, 'preview', ['fixture' => 'small']);
        $completed = $service->complete($event, 10, 8, ['Two rows need review.']);

        $this->assertSame($source->organization_id, $completed->organization_id);
        $this->assertSame('completed', $completed->status);
        $this->assertSame(10, $completed->total_records);
        $this->assertSame(8, $completed->processed_records);
        $this->assertSame(2, $completed->failed_records);
        $this->assertSame(['Two rows need review.'], $completed->warnings);
        $this->assertNotNull($source->refresh()->last_preview_at);
        $this->assertTrue($source->events()->whereKey($completed->id)->exists());
    }

    public function test_successful_sync_event_updates_last_synced_at(): void
    {
        $source = MallSyncSource::factory()->create();
        $service = app(MallSyncEventService::class);

        $completed = $service->complete($service->start($source, 'sync'), 5, 5);

        $this->assertSame('completed', $completed->status);
        $this->assertSame(0, $completed->failed_records);
        $this->assertNotNull($source->refresh()->last_synced_at);
    }

    public function test_failed_sync_event_records_errors_without_updating_last_sync(): void
    {
        $source = MallSyncSource::factory()->create();
        $event = app(MallSyncEventService::class)->start($source, 'sync');

        $failed = app(MallSyncEventService::class)->fail($event, ['Invalid source payload.']);

        $this->assertSame('failed', $failed->status);
        $this->assertSame(['Invalid source payload.'], $failed->errors);
        $this->assertNull($source->refresh()->last_synced_at);
        $this->assertTrue($source->organization->mallSyncEvents()->whereKey($failed->id)->exists());
    }
}
