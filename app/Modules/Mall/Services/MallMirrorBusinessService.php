<?php

namespace App\Modules\Mall\Services;

use App\Models\BusinessProfile;
use App\Models\MallMirrorBusiness;
use App\Models\MallPublicationConsent;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MallMirrorBusinessService
{
    public function createDraft(BusinessProfile $profile, ?MallPublicationConsent $consent = null): MallMirrorBusiness
    {
        if ($consent !== null && (int) $consent->organization_id !== (int) $profile->organization_id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the business organization.',
            ]);
        }

        return MallMirrorBusiness::query()->updateOrCreate(
            [
                'organization_id' => $profile->organization_id,
                'slug' => $profile->slug,
            ],
            [
                'company_id' => $profile->company_id,
                'site_id' => $profile->site_id,
                'business_profile_id' => $profile->id,
                'mall_publication_consent_id' => $consent?->id,
                'display_name' => $profile->display_name,
                'description' => $profile->description,
                'public_contacts' => [
                    'email' => $profile->public_email,
                    'phone' => $profile->public_phone,
                    'website_url' => $profile->website_url,
                ],
                'mirror_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(MallMirrorBusiness $business): MallMirrorBusiness
    {
        $consent = $business->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'business_directory')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Business directory publication requires granted consent.',
            ]);
        }

        $business->forceFill([
            'mirror_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $business->refresh();
    }

    public function unpublish(MallMirrorBusiness $business): MallMirrorBusiness
    {
        $business->forceFill([
            'mirror_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $business->refresh();
    }

    public function regenerateSlug(BusinessProfile $profile): string
    {
        return Str::slug($profile->display_name).'-'.$profile->id;
    }
}
