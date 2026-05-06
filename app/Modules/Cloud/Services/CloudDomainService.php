<?php

namespace App\Modules\Cloud\Services;

use App\Models\CloudDomain;
use App\Models\CloudSite;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CloudDomainService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Organization $organization, Site $site, string $domain, array $attributes = []): CloudDomain
    {
        if ((int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
            ]);
        }

        if (($attributes['cloud_site_id'] ?? null) !== null) {
            $cloudSite = CloudSite::query()->findOrFail($attributes['cloud_site_id']);

            if ((int) $cloudSite->organization_id !== (int) $organization->id || (int) $cloudSite->site_id !== (int) $site->id) {
                throw ValidationException::withMessages([
                    'cloud_site_id' => 'The selected cloud site does not belong to this site.',
                ]);
            }
        }

        return CloudDomain::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'domain' => mb_strtolower($domain),
            ],
            [
                ...$attributes,
                'site_id' => $site->id,
                'domain_type' => $attributes['domain_type'] ?? 'custom',
                'verification_status' => $attributes['verification_status'] ?? 'pending',
                'dns_status' => $attributes['dns_status'] ?? 'unknown',
                'ssl_status' => $attributes['ssl_status'] ?? 'not_requested',
            ],
        );
    }

    public function markVerified(CloudDomain $domain): CloudDomain
    {
        $domain->forceFill([
            'verification_status' => 'verified',
            'dns_status' => 'valid',
            'ssl_status' => 'ready',
            'verified_at' => now(),
            'last_checked_at' => now(),
        ])->save();

        return $domain->refresh();
    }

    public function makePrimary(CloudDomain $domain): CloudDomain
    {
        if ($domain->verification_status !== 'verified') {
            throw ValidationException::withMessages([
                'verification_status' => 'Only verified domains can be primary.',
            ]);
        }

        return DB::transaction(function () use ($domain): CloudDomain {
            CloudDomain::query()
                ->where('site_id', $domain->site_id)
                ->whereKeyNot($domain->id)
                ->update(['is_primary' => false]);

            $domain->forceFill(['is_primary' => true])->save();

            return $domain->refresh();
        });
    }
}
