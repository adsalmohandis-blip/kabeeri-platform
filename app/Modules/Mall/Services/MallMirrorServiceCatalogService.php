<?php

namespace App\Modules\Mall\Services;

use App\Models\MallMirrorService;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class MallMirrorServiceCatalogService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes, ?MallPublicationConsent $consent = null): MallMirrorService
    {
        if ($consent !== null && (int) $consent->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the service organization.',
            ]);
        }

        return MallMirrorService::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => $attributes['slug'],
            ],
            [
                ...$attributes,
                'company_id' => $attributes['company_id'] ?? null,
                'site_id' => $attributes['site_id'] ?? null,
                'mall_publication_consent_id' => $consent?->id,
                'service_name' => $attributes['service_name'],
                'currency' => $attributes['currency'] ?? 'USD',
                'mirror_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(MallMirrorService $service): MallMirrorService
    {
        $consent = $service->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'services')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Service publication requires granted Mall services consent.',
            ]);
        }

        $service->forceFill([
            'mirror_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $service->refresh();
    }

    public function unpublish(MallMirrorService $service): MallMirrorService
    {
        $service->forceFill([
            'mirror_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $service->refresh();
    }
}
