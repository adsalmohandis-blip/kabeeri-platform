<?php

namespace App\Modules\Platform\Services;

use App\Models\ExternalObjectLink;
use App\Models\IntegrationConnector;
use App\Models\IntegrationCredential;
use App\Models\IntegrationSyncJob;
use App\Models\IntegrationSyncLog;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class IntegrationHubService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function registerConnector(?Organization $organization, array $attributes): IntegrationConnector
    {
        return IntegrationConnector::query()->create([
            ...$attributes,
            'organization_id' => $organization?->id,
            'status' => $attributes['status'] ?? 'draft',
            'connector_type' => $attributes['connector_type'] ?? 'api',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function storeCredentialReference(Organization $organization, IntegrationConnector $connector, array $attributes): IntegrationCredential
    {
        if (isset($attributes['secret']) || isset($attributes['token']) || isset($attributes['password'])) {
            throw ValidationException::withMessages([
                'vault_reference' => 'V5 Integration Hub stores vault references only, never raw secrets.',
            ]);
        }

        return IntegrationCredential::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'credential_type' => $attributes['credential_type'] ?? 'api_key_reference',
            'status' => $attributes['status'] ?? 'inactive',
        ]);
    }

    public function linkExternalObject(Organization $organization, IntegrationConnector $connector, Model $local, string $type, string $externalId): ExternalObjectLink
    {
        return ExternalObjectLink::query()->create([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'linkable_type' => $local->getMorphClass(),
            'linkable_id' => $local->getKey(),
            'external_object_type' => $type,
            'external_object_id' => $externalId,
            'sync_direction' => 'preview',
            'status' => 'linked',
            'last_seen_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function queuePreviewSync(Organization $organization, IntegrationConnector $connector, array $payload = []): IntegrationSyncJob
    {
        return IntegrationSyncJob::query()->create([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'job_type' => 'preview_import',
            'status' => 'queued',
            'direction' => 'import',
            'scheduled_at' => now(),
            'payload' => $payload,
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function log(Organization $organization, ?IntegrationSyncJob $job, string $message, string $level = 'info', array $context = []): IntegrationSyncLog
    {
        return IntegrationSyncLog::query()->create([
            'organization_id' => $organization->id,
            'integration_sync_job_id' => $job?->id,
            'integration_connector_id' => $job?->integration_connector_id,
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
