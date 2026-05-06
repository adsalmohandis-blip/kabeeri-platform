<?php

namespace App\Modules\Cloud\Services;

use App\Models\CloudHealthCheck;
use App\Models\CloudSite;

class CloudHealthCheckService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function record(CloudSite $cloudSite, array $attributes): CloudHealthCheck
    {
        $status = $attributes['status'] ?? 'unknown';

        $check = CloudHealthCheck::query()->create([
            ...$attributes,
            'organization_id' => $cloudSite->organization_id,
            'site_id' => $cloudSite->site_id,
            'cloud_site_id' => $cloudSite->id,
            'check_type' => $attributes['check_type'] ?? 'manual',
            'status' => $status,
            'checked_at' => $attributes['checked_at'] ?? now(),
        ]);

        $cloudSite->forceFill([
            'health_status' => $status,
            'last_checked_at' => $check->checked_at,
        ])->save();

        return $check->refresh();
    }
}
