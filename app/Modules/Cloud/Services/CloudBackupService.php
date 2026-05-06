<?php

namespace App\Modules\Cloud\Services;

use App\Models\CloudBackup;
use App\Models\CloudSite;

class CloudBackupService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createManualBackup(CloudSite $cloudSite, array $attributes = []): CloudBackup
    {
        return CloudBackup::query()->create([
            ...$attributes,
            'organization_id' => $cloudSite->organization_id,
            'site_id' => $cloudSite->site_id,
            'cloud_site_id' => $cloudSite->id,
            'backup_type' => $attributes['backup_type'] ?? 'manual',
            'status' => 'pending',
            'started_at' => $attributes['started_at'] ?? now(),
        ]);
    }

    public function markCompleted(CloudBackup $backup, string $reference, ?int $sizeBytes = null): CloudBackup
    {
        $backup->forceFill([
            'status' => 'completed',
            'backup_reference' => $reference,
            'size_bytes' => $sizeBytes,
            'completed_at' => now(),
            'failed_at' => null,
            'errors' => null,
        ])->save();

        return $backup->refresh();
    }

    /**
     * @param  list<string>  $errors
     */
    public function markFailed(CloudBackup $backup, array $errors): CloudBackup
    {
        $backup->forceFill([
            'status' => 'failed',
            'failed_at' => now(),
            'errors' => $errors,
        ])->save();

        return $backup->refresh();
    }
}
