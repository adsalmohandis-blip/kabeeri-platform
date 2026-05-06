<?php

namespace App\Modules\Mall\Services;

use App\Models\ExternalSource;
use App\Models\MallSyncSource;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Validation\ValidationException;

class MallSyncSourceService
{
    private const SECRET_KEYS = ['token', 'secret', 'api_key', 'password', 'client_secret'];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, ?Site $site = null, ?ExternalSource $externalSource = null, array $attributes = []): MallSyncSource
    {
        $this->assertTenantMatch($organization, $site, $externalSource);
        $this->assertNoPlainSecrets($attributes['settings'] ?? []);

        return MallSyncSource::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'site_id' => $site?->id ?? $externalSource?->site_id,
            'external_source_id' => $externalSource?->id,
            'source_name' => $attributes['source_name'] ?? $externalSource?->source_name ?? 'Manual Mall Source',
            'source_type' => $attributes['source_type'] ?? $externalSource?->source_type ?? 'manual',
            'sync_scope' => $attributes['sync_scope'] ?? 'business_directory',
            'sync_direction' => $attributes['sync_direction'] ?? 'mirror_to_mall',
            'status' => 'draft',
        ]);
    }

    public function markPreviewed(MallSyncSource $source): MallSyncSource
    {
        $source->forceFill([
            'status' => 'previewed',
            'last_preview_at' => now(),
        ])->save();

        return $source->refresh();
    }

    public function activate(MallSyncSource $source): MallSyncSource
    {
        if ($source->last_preview_at === null) {
            throw ValidationException::withMessages([
                'last_preview_at' => 'Mall sync source must be previewed before activation.',
            ]);
        }

        $source->forceFill(['status' => 'active'])->save();

        return $source->refresh();
    }

    private function assertTenantMatch(Organization $organization, ?Site $site, ?ExternalSource $externalSource): void
    {
        if ($site !== null && (int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
            ]);
        }

        if ($externalSource !== null && (int) $externalSource->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'external_source_id' => 'The selected external source does not belong to this organization.',
            ]);
        }
    }

    private function assertNoPlainSecrets(mixed $settings): void
    {
        if (! is_array($settings)) {
            return;
        }

        foreach (array_keys($settings) as $key) {
            if (in_array(strtolower((string) $key), self::SECRET_KEYS, true)) {
                throw ValidationException::withMessages([
                    'settings' => 'Mall sync source settings must not contain plain-text secrets.',
                ]);
            }
        }
    }
}
