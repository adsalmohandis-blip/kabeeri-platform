<?php

namespace App\Modules\Mall\Services;

use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class MallPublicationConsentService
{
    /**
     * @param  list<string>  $channels
     * @param  array<string, mixed>  $attributes
     */
    public function request(
        Organization $organization,
        ?Site $site = null,
        ?Model $subject = null,
        array $channels = ['business_directory'],
        array $attributes = []
    ): MallPublicationConsent {
        $this->assertTenantMatch($organization, $site, $subject);

        return MallPublicationConsent::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $attributes['company_id'] ?? $site?->company_id,
            'site_id' => $site?->id,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'consent_type' => $attributes['consent_type'] ?? 'mall_publication',
            'status' => 'pending',
            'channels' => $channels,
        ]);
    }

    public function grant(MallPublicationConsent $consent, User $user): MallPublicationConsent
    {
        $consent->forceFill([
            'status' => 'granted',
            'granted_by' => $user->id,
            'granted_at' => now(),
            'revoked_at' => null,
        ])->save();

        return $consent->refresh();
    }

    public function revoke(MallPublicationConsent $consent): MallPublicationConsent
    {
        $consent->forceFill([
            'status' => 'revoked',
            'revoked_at' => now(),
        ])->save();

        return $consent->refresh();
    }

    public function allowsPublication(MallPublicationConsent $consent, string $channel): bool
    {
        return $consent->status === 'granted'
            && in_array($channel, $consent->channels ?? [], true);
    }

    private function assertTenantMatch(Organization $organization, ?Site $site, ?Model $subject): void
    {
        if ($site !== null && (int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
            ]);
        }

        if ($subject !== null && isset($subject->organization_id) && (int) $subject->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'subject_id' => 'The selected publication subject does not belong to this organization.',
            ]);
        }
    }
}
