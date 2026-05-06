<?php

namespace App\Modules\Platform\Services;

use App\Models\DesktopClient;
use App\Models\DesktopFileQueueItem;
use App\Models\DesktopOutboxOperation;
use App\Models\DesktopSyncConflict;
use App\Models\DesktopSyncSession;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Str;

class DesktopSyncService
{
    public function registerClient(?User $user, array $attributes): DesktopClient
    {
        return DesktopClient::query()->updateOrCreate(
            ['client_uuid' => $attributes['client_uuid']],
            [
                'user_id' => $user?->id,
                'organization_id' => $attributes['organization_id'] ?? null,
                'name' => $attributes['name'] ?? null,
                'platform' => $attributes['platform'] ?? 'unknown',
                'app_version' => $attributes['app_version'] ?? null,
                'status' => 'active',
                'last_seen_at' => now(),
                'capabilities' => $attributes['capabilities'] ?? ['pull', 'push_dry_run', 'file_queue'],
                'metadata' => $attributes['metadata'] ?? ['source' => 'v8_desktop'],
            ],
        );
    }

    public function startSession(DesktopClient $client, string $direction = 'bidirectional', ?string $cursor = null): DesktopSyncSession
    {
        return DesktopSyncSession::query()->create([
            'desktop_client_id' => $client->id,
            'session_uuid' => (string) Str::uuid(),
            'direction' => $direction,
            'status' => 'active',
            'cursor' => $cursor,
            'started_at' => now(),
            'summary' => ['pulls' => 0, 'push_dry_runs' => 0, 'conflicts' => 0, 'queued_files' => 0],
            'metadata' => ['source' => 'v8_desktop_sync'],
        ]);
    }

    public function pull(DesktopSyncSession $session, array $collections = ['modules'], ?string $since = null): array
    {
        $changes = [];

        if (in_array('modules', $collections, true)) {
            $changes['modules'] = Module::query()
                ->orderBy('key')
                ->get(['key', 'name', 'status', 'version', 'updated_at'])
                ->map(fn (Module $module): array => [
                    'type' => 'module',
                    'id' => $module->key,
                    'version' => $module->version,
                    'status' => $module->status,
                    'updated_at' => $module->updated_at?->toISOString(),
                ])
                ->all();
        }

        $cursor = now()->toISOString();
        $summary = $session->summary ?? [];
        $summary['pulls'] = ($summary['pulls'] ?? 0) + 1;

        $session->update([
            'cursor' => $cursor,
            'last_pull_at' => now(),
            'summary' => $summary,
        ]);

        return [
            'session_uuid' => $session->session_uuid,
            'cursor' => $cursor,
            'since' => $since,
            'collections' => $collections,
            'changes' => $changes,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $operations
     */
    public function dryRunPush(DesktopSyncSession $session, array $operations): array
    {
        $accepted = 0;
        $conflicts = 0;
        $results = [];

        foreach ($operations as $index => $operation) {
            $clientOperationId = (string) ($operation['client_operation_id'] ?? 'op-'.$index.'-'.Str::uuid());
            $baseVersion = array_key_exists('base_version', $operation) ? (int) $operation['base_version'] : null;
            $serverVersion = (int) ($operation['server_version'] ?? 1);
            $hasConflict = $baseVersion !== null && $baseVersion < $serverVersion;

            $record = DesktopOutboxOperation::query()->updateOrCreate(
                [
                    'desktop_sync_session_id' => $session->id,
                    'client_operation_id' => $clientOperationId,
                ],
                [
                    'operation_type' => (string) ($operation['operation_type'] ?? 'upsert'),
                    'entity_type' => (string) ($operation['entity_type'] ?? 'unknown'),
                    'entity_id' => isset($operation['entity_id']) ? (string) $operation['entity_id'] : null,
                    'base_version' => $baseVersion,
                    'server_version' => $serverVersion,
                    'status' => $hasConflict ? 'conflict' : 'accepted_dry_run',
                    'payload_preview' => $operation['payload'] ?? null,
                    'validation_errors' => null,
                    'received_at' => now(),
                ],
            );

            if ($hasConflict) {
                $conflicts++;
                DesktopSyncConflict::query()->create([
                    'desktop_sync_session_id' => $session->id,
                    'desktop_outbox_operation_id' => $record->id,
                    'entity_type' => $record->entity_type,
                    'entity_id' => $record->entity_id,
                    'conflict_type' => 'version_mismatch',
                    'status' => 'open',
                    'client_snapshot' => ['base_version' => $baseVersion, 'payload' => $operation['payload'] ?? null],
                    'server_snapshot' => ['server_version' => $serverVersion],
                ]);
            } else {
                $accepted++;
            }

            $results[] = [
                'client_operation_id' => $clientOperationId,
                'status' => $record->status,
                'dry_run_only' => true,
            ];
        }

        $summary = $session->summary ?? [];
        $summary['push_dry_runs'] = ($summary['push_dry_runs'] ?? 0) + 1;
        $summary['conflicts'] = ($summary['conflicts'] ?? 0) + $conflicts;
        $session->update(['last_push_dry_run_at' => now(), 'summary' => $summary]);

        return [
            'session_uuid' => $session->session_uuid,
            'dry_run_only' => true,
            'accepted' => $accepted,
            'conflicts' => $conflicts,
            'results' => $results,
        ];
    }

    public function queueFile(DesktopClient $client, array $attributes, ?DesktopSyncSession $session = null): DesktopFileQueueItem
    {
        $hash = $attributes['sha256'] ?? null;

        $item = DesktopFileQueueItem::query()->create([
            'desktop_client_id' => $client->id,
            'desktop_sync_session_id' => $session?->id,
            'queue_uuid' => (string) Str::uuid(),
            'direction' => $attributes['direction'] ?? 'upload',
            'filename' => $attributes['filename'],
            'mime_type' => $attributes['mime_type'] ?? null,
            'size_bytes' => $attributes['size_bytes'] ?? 0,
            'sha256' => $hash,
            'status' => 'queued',
            'storage_reference' => $hash ? 'filequeue://sha256/'.$hash : null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);

        if ($session) {
            $summary = $session->summary ?? [];
            $summary['queued_files'] = ($summary['queued_files'] ?? 0) + 1;
            $session->update(['summary' => $summary]);
        }

        return $item;
    }
}
