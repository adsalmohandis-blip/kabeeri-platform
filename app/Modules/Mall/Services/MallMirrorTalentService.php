<?php

namespace App\Modules\Mall\Services;

use App\Models\EmployeeProfile;
use App\Models\MallMirrorTalent;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class MallMirrorTalentService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes, ?EmployeeProfile $employee = null, ?MallPublicationConsent $consent = null): MallMirrorTalent
    {
        if ($employee !== null && (int) $employee->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'employee_profile_id' => 'Employee profile must belong to the talent organization.',
            ]);
        }

        if ($consent !== null && (int) $consent->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the talent organization.',
            ]);
        }

        return MallMirrorTalent::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => $attributes['slug'],
            ],
            [
                ...$attributes,
                'company_id' => $attributes['company_id'] ?? $employee?->company_id,
                'site_id' => $attributes['site_id'] ?? null,
                'employee_profile_id' => $employee?->id,
                'mall_publication_consent_id' => $consent?->id,
                'display_name' => $attributes['display_name'] ?? $employee?->full_name,
                'mirror_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(MallMirrorTalent $talent): MallMirrorTalent
    {
        $consent = $talent->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'talent')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Talent publication requires granted Mall talent consent.',
            ]);
        }

        $talent->forceFill([
            'mirror_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $talent->refresh();
    }

    public function unpublish(MallMirrorTalent $talent): MallMirrorTalent
    {
        $talent->forceFill([
            'mirror_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $talent->refresh();
    }
}
