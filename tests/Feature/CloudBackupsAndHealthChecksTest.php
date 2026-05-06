<?php

namespace Tests\Feature;

use App\Models\CloudSite;
use App\Modules\Cloud\Services\CloudBackupService;
use App\Modules\Cloud\Services\CloudHealthCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloudBackupsAndHealthChecksTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloud_backup_can_be_tracked_without_storing_binary_data(): void
    {
        $cloudSite = CloudSite::factory()->create();

        $backup = app(CloudBackupService::class)->createManualBackup($cloudSite, [
            'storage_label' => 'local-reference',
            'metadata' => ['scope' => 'metadata_only'],
        ]);

        $completed = app(CloudBackupService::class)->markCompleted($backup, 'backups/site-demo-001', 2048);

        $this->assertSame($cloudSite->organization_id, $completed->organization_id);
        $this->assertSame($cloudSite->site_id, $completed->site_id);
        $this->assertSame('completed', $completed->status);
        $this->assertSame('backups/site-demo-001', $completed->backup_reference);
        $this->assertSame(2048, $completed->size_bytes);
        $this->assertTrue($cloudSite->backups()->whereKey($completed->id)->exists());
    }

    public function test_cloud_backup_failures_are_recorded_as_metadata(): void
    {
        $backup = app(CloudBackupService::class)->createManualBackup(CloudSite::factory()->create());

        $failed = app(CloudBackupService::class)->markFailed($backup, ['Storage reference unavailable.']);

        $this->assertSame('failed', $failed->status);
        $this->assertSame(['Storage reference unavailable.'], $failed->errors);
        $this->assertNotNull($failed->failed_at);
    }

    public function test_health_check_updates_cloud_site_health_summary(): void
    {
        $cloudSite = CloudSite::factory()->create(['health_status' => 'unknown']);

        $check = app(CloudHealthCheckService::class)->record($cloudSite, [
            'check_type' => 'http',
            'status' => 'healthy',
            'status_code' => 200,
            'response_time_ms' => 42,
            'message' => 'OK',
        ]);

        $this->assertSame($cloudSite->organization_id, $check->organization_id);
        $this->assertSame('healthy', $check->status);
        $this->assertSame(200, $check->status_code);
        $this->assertSame(42, $check->response_time_ms);
        $this->assertSame('healthy', $cloudSite->refresh()->health_status);
        $this->assertTrue($cloudSite->healthChecks()->whereKey($check->id)->exists());
    }
}
