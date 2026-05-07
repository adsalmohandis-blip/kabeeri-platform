<?php

namespace Database\Seeders;

use App\Modules\Platform\Services\DesktopSyncService;
use Illuminate\Database\Seeder;

class V8DemoSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(DesktopSyncService::class);
        $client = $service->registerClient(null, [
            'client_uuid' => 'kabeeri-desktop-demo',
            'name' => 'kabeeri Desktop Demo',
            'platform' => 'windows',
            'app_version' => '1.0.0',
            'capabilities' => ['pull', 'push_dry_run', 'file_queue'],
            'metadata' => ['source' => 'v8_demo'],
        ]);

        if (! $client->wasRecentlyCreated) {
            return;
        }

        $session = $service->startSession($client);
        $service->pull($session, ['modules']);
        $service->queueFile($client, [
            'filename' => 'demo-export.json',
            'mime_type' => 'application/json',
            'size_bytes' => 128,
            'sha256' => hash('sha256', 'demo-export.json'),
            'metadata' => ['source' => 'v8_demo'],
        ], $session);
    }
}
