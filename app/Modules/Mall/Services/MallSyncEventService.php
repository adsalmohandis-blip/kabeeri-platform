<?php

namespace App\Modules\Mall\Services;

use App\Models\MallSyncEvent;
use App\Models\MallSyncSource;

class MallSyncEventService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function start(MallSyncSource $source, string $eventType, array $metadata = []): MallSyncEvent
    {
        return MallSyncEvent::query()->create([
            'organization_id' => $source->organization_id,
            'mall_sync_source_id' => $source->id,
            'event_type' => $eventType,
            'status' => 'running',
            'metadata' => $metadata,
            'started_at' => now(),
        ]);
    }

    /**
     * @param  list<string>  $warnings
     */
    public function complete(MallSyncEvent $event, int $totalRecords, int $processedRecords, array $warnings = []): MallSyncEvent
    {
        $event->forceFill([
            'status' => 'completed',
            'total_records' => $totalRecords,
            'processed_records' => $processedRecords,
            'failed_records' => max(0, $totalRecords - $processedRecords),
            'warnings' => $warnings ?: null,
            'completed_at' => now(),
        ])->save();

        if ($event->event_type === 'preview') {
            $event->mallSyncSource->forceFill(['last_preview_at' => $event->completed_at])->save();
        }

        if ($event->event_type === 'sync' && $event->failed_records === 0) {
            $event->mallSyncSource->forceFill(['last_synced_at' => $event->completed_at])->save();
        }

        return $event->refresh();
    }

    /**
     * @param  list<string>  $errors
     */
    public function fail(MallSyncEvent $event, array $errors): MallSyncEvent
    {
        $event->forceFill([
            'status' => 'failed',
            'errors' => $errors,
            'completed_at' => now(),
        ])->save();

        return $event->refresh();
    }
}
