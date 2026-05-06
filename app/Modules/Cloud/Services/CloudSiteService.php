<?php

namespace App\Modules\Cloud\Services;

use App\Models\CloudSite;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Validation\ValidationException;

class CloudSiteService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForSite(Organization $organization, Site $site, array $attributes = []): CloudSite
    {
        if ((int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
            ]);
        }

        return CloudSite::query()->updateOrCreate(
            [
                'site_id' => $site->id,
                'environment' => $attributes['environment'] ?? 'production',
            ],
            [
                ...$attributes,
                'organization_id' => $organization->id,
                'deployment_status' => $attributes['deployment_status'] ?? 'draft',
                'health_status' => $attributes['health_status'] ?? 'unknown',
            ],
        );
    }

    public function markDeploying(CloudSite $cloudSite): CloudSite
    {
        $cloudSite->forceFill([
            'deployment_status' => 'deploying',
            'health_status' => 'unknown',
        ])->save();

        return $cloudSite->refresh();
    }

    public function markDeployed(CloudSite $cloudSite, ?string $publicUrl = null): CloudSite
    {
        $cloudSite->forceFill([
            'deployment_status' => 'deployed',
            'health_status' => 'healthy',
            'public_url' => $publicUrl ?? $cloudSite->public_url,
            'last_deployed_at' => now(),
            'last_checked_at' => now(),
        ])->save();

        return $cloudSite->refresh();
    }

    public function markUnhealthy(CloudSite $cloudSite, ?string $reason = null): CloudSite
    {
        $metadata = $cloudSite->metadata ?? [];

        if ($reason !== null) {
            $metadata['last_health_warning'] = $reason;
        }

        $cloudSite->forceFill([
            'health_status' => 'unhealthy',
            'last_checked_at' => now(),
            'metadata' => $metadata,
        ])->save();

        return $cloudSite->refresh();
    }
}
