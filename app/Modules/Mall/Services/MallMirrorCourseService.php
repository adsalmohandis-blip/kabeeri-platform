<?php

namespace App\Modules\Mall\Services;

use App\Models\MallMirrorCourse;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class MallMirrorCourseService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes, ?MallPublicationConsent $consent = null): MallMirrorCourse
    {
        if ($consent !== null && (int) $consent->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the course organization.',
            ]);
        }

        return MallMirrorCourse::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => $attributes['slug'],
            ],
            [
                ...$attributes,
                'company_id' => $attributes['company_id'] ?? null,
                'site_id' => $attributes['site_id'] ?? null,
                'mall_publication_consent_id' => $consent?->id,
                'course_name' => $attributes['course_name'],
                'currency' => $attributes['currency'] ?? 'USD',
                'mirror_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(MallMirrorCourse $course): MallMirrorCourse
    {
        $consent = $course->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'courses')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Course publication requires granted Mall courses consent.',
            ]);
        }

        $course->forceFill([
            'mirror_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $course->refresh();
    }

    public function unpublish(MallMirrorCourse $course): MallMirrorCourse
    {
        $course->forceFill([
            'mirror_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $course->refresh();
    }
}
