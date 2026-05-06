<?php

namespace App\Modules\Rabet\Services;

use App\Models\AcademyBadge;
use App\Models\AcademyBadgeAward;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkNetworkProfile;
use Illuminate\Validation\ValidationException;

class AcademyBadgeService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function award(Organization $organization, AcademyBadge $badge, ?User $user = null, ?WorkNetworkProfile $profile = null, ?User $awardedBy = null, array $attributes = []): AcademyBadgeAward
    {
        if ($badge->status !== 'active') {
            throw ValidationException::withMessages([
                'academy_badge_id' => 'Only active academy badges can be awarded.',
            ]);
        }

        if ($profile !== null && (int) $profile->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'work_network_profile_id' => 'The work profile does not belong to this organization.',
            ]);
        }

        if ($user === null && $profile === null) {
            throw ValidationException::withMessages([
                'recipient' => 'An academy badge award requires a user or work network profile.',
            ]);
        }

        return AcademyBadgeAward::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'academy_badge_id' => $badge->id,
            'user_id' => $user?->id ?? $profile?->user_id,
            'work_network_profile_id' => $profile?->id,
            'awarded_by_user_id' => $awardedBy?->id,
            'status' => 'active',
            'awarded_at' => now(),
        ]);
    }

    public function revoke(AcademyBadgeAward $award, ?string $reason = null): AcademyBadgeAward
    {
        $metadata = $award->metadata ?? [];

        if ($reason !== null) {
            $metadata['revoked_reason'] = $reason;
        }

        $award->forceFill([
            'status' => 'revoked',
            'metadata' => $metadata,
        ])->save();

        return $award->refresh();
    }
}
