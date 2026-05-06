<?php

namespace Tests\Feature;

use App\Models\DesktopClient;
use App\Models\DesktopFileQueueItem;
use App\Models\DesktopOutboxOperation;
use App\Models\DesktopSyncConflict;
use App\Models\DesktopSyncSession;
use App\Modules\Platform\Services\DesktopSyncService;
use Database\Seeders\ModulesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class V8DesktopSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_v8_desktop_tables_exist(): void
    {
        foreach ([
            'desktop_clients',
            'desktop_sync_sessions',
            'desktop_outbox_operations',
            'desktop_sync_conflicts',
            'desktop_file_queue_items',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing V8 table [{$table}].");
        }
    }

    public function test_desktop_service_registers_client_session_pull_dry_run_conflict_and_file_queue(): void
    {
        $this->seed(ModulesSeeder::class);
        $service = app(DesktopSyncService::class);
        $client = $service->registerClient(null, [
            'client_uuid' => 'desktop-client-1',
            'name' => 'Ops Laptop',
            'platform' => 'windows',
            'app_version' => '1.0.0',
        ]);
        $session = $service->startSession($client);
        $pull = $service->pull($session, ['modules']);
        $push = $service->dryRunPush($session, [[
            'client_operation_id' => 'op-1',
            'operation_type' => 'upsert',
            'entity_type' => 'module_note',
            'entity_id' => 'core',
            'base_version' => 1,
            'server_version' => 2,
            'payload' => ['note' => 'local draft'],
        ], [
            'client_operation_id' => 'op-2',
            'operation_type' => 'upsert',
            'entity_type' => 'module_note',
            'entity_id' => 'desktop_platform',
            'base_version' => 2,
            'server_version' => 2,
            'payload' => ['note' => 'safe dry run'],
        ]]);
        $file = $service->queueFile($client, [
            'filename' => 'ledger-export.csv',
            'mime_type' => 'text/csv',
            'size_bytes' => 512,
            'sha256' => hash('sha256', 'ledger-export.csv'),
        ], $session);

        $this->assertSame('active', $client->status);
        $this->assertSame($session->session_uuid, $pull['session_uuid']);
        $this->assertArrayHasKey('modules', $pull['changes']);
        $this->assertTrue($push['dry_run_only']);
        $this->assertSame(1, $push['accepted']);
        $this->assertSame(1, $push['conflicts']);
        $this->assertSame('queued', $file->status);
        $this->assertSame('filequeue://sha256/'.hash('sha256', 'ledger-export.csv'), $file->storage_reference);
        $this->assertSame(2, DesktopOutboxOperation::query()->count());
        $this->assertSame(1, DesktopSyncConflict::query()->where('status', 'open')->count());
        $this->assertSame(1, DesktopFileQueueItem::query()->count());
    }

    public function test_desktop_apis_cover_register_pull_push_dry_run_and_file_queue(): void
    {
        $this->seed(ModulesSeeder::class);

        $registered = $this->postJson('/api/desktop/register', [
            'client_uuid' => 'desktop-api-client',
            'name' => 'Desktop API Client',
            'platform' => 'macos',
            'app_version' => '1.0.0',
        ])->assertCreated()
            ->assertJsonPath('status', 'active')
            ->json();

        $this->postJson('/api/desktop/sync/pull', [
            'session_id' => $registered['session_id'],
            'collections' => ['modules'],
        ])->assertOk()
            ->assertJsonPath('session_uuid', $registered['session_uuid']);

        $this->postJson('/api/desktop/sync/push-dry-run', [
            'session_id' => $registered['session_id'],
            'operations' => [[
                'client_operation_id' => 'api-op-1',
                'operation_type' => 'update',
                'entity_type' => 'module_note',
                'entity_id' => 'core',
                'base_version' => 0,
                'server_version' => 1,
                'payload' => ['note' => 'desktop local draft'],
            ]],
        ])->assertOk()
            ->assertJsonPath('dry_run_only', true)
            ->assertJsonPath('conflicts', 1);

        $this->postJson('/api/desktop/files', [
            'client_id' => $registered['client_id'],
            'session_id' => $registered['session_id'],
            'filename' => 'snapshot.zip',
            'mime_type' => 'application/zip',
            'size_bytes' => 1024,
            'sha256' => hash('sha256', 'snapshot.zip'),
        ])->assertCreated()
            ->assertJsonPath('status', 'queued');

        $this->assertSame(1, DesktopClient::query()->count());
        $this->assertSame(1, DesktopSyncSession::query()->count());
        $this->assertSame(1, DesktopOutboxOperation::query()->count());
        $this->assertSame(1, DesktopSyncConflict::query()->count());
        $this->assertSame(1, DesktopFileQueueItem::query()->count());
    }

    public function test_push_api_is_dry_run_only_and_does_not_apply_payload_as_domain_data(): void
    {
        $service = app(DesktopSyncService::class);
        $client = $service->registerClient(null, ['client_uuid' => 'dry-run-guard']);
        $session = $service->startSession($client);

        $response = $this->postJson('/api/desktop/sync/push-dry-run', [
            'session_id' => $session->id,
            'operations' => [[
                'client_operation_id' => 'guard-op',
                'operation_type' => 'delete',
                'entity_type' => 'users',
                'entity_id' => '1',
                'base_version' => 1,
                'server_version' => 1,
                'payload' => ['dangerous' => true],
            ]],
        ])->assertOk()->json();

        $this->assertTrue($response['dry_run_only']);
        $this->assertDatabaseHas('desktop_outbox_operations', [
            'client_operation_id' => 'guard-op',
            'status' => 'accepted_dry_run',
        ]);
        $this->assertSame(0, DesktopSyncConflict::query()->count());
    }
}
