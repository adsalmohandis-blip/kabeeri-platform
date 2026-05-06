<?php

namespace App\Modules\Mall\Services;

use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\TravelTourismMallListing;
use Illuminate\Validation\ValidationException;

class TravelTourismMallListingService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes, ?MallPublicationConsent $consent = null): TravelTourismMallListing
    {
        if ($consent !== null && (int) $consent->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the travel listing organization.',
            ]);
        }

        return TravelTourismMallListing::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => $attributes['slug'],
            ],
            [
                ...$attributes,
                'company_id' => $attributes['company_id'] ?? null,
                'site_id' => $attributes['site_id'] ?? null,
                'mall_publication_consent_id' => $consent?->id,
                'title' => $attributes['title'],
                'listing_type' => $attributes['listing_type'] ?? 'tour',
                'currency' => $attributes['currency'] ?? 'USD',
                'listing_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(TravelTourismMallListing $listing): TravelTourismMallListing
    {
        $consent = $listing->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'travel_tourism')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Travel and tourism publication requires granted Mall travel consent.',
            ]);
        }

        $listing->forceFill([
            'listing_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $listing->refresh();
    }

    public function unpublish(TravelTourismMallListing $listing): TravelTourismMallListing
    {
        $listing->forceFill([
            'listing_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $listing->refresh();
    }
}
