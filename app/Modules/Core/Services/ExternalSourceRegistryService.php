<?php

namespace App\Modules\Core\Services;

use App\Models\Company;
use App\Models\ExternalSource;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Validation\ValidationException;

class ExternalSourceRegistryService
{
    private const SECRET_KEYS = ['token', 'secret', 'api_key', 'password', 'client_secret'];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Organization $organization, array $attributes, ?Company $company = null, ?Site $site = null): ExternalSource
    {
        $this->assertTenantMatch($organization, $company, $site);
        $this->assertNoPlainSecrets($attributes['settings'] ?? []);

        return ExternalSource::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $company?->id,
            'site_id' => $site?->id,
            'source_type' => $attributes['source_type'] ?? 'manual',
            'source_name' => $attributes['source_name'],
            'connection_type' => $attributes['connection_type'] ?? 'manual',
            'status' => $attributes['status'] ?? 'draft',
        ]);
    }

    public function markPreviewed(ExternalSource $source): ExternalSource
    {
        $source->forceFill([
            'status' => 'previewed',
            'last_sync_at' => null,
        ])->save();

        return $source->refresh();
    }

    private function assertTenantMatch(Organization $organization, ?Company $company, ?Site $site): void
    {
        if ($company !== null && (int) $company->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'company_id' => 'The selected company does not belong to this organization.',
            ]);
        }

        if ($site !== null && (int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
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
                    'settings' => 'External source settings must not contain plain-text secrets.',
                ]);
            }
        }
    }
}
