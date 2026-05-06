<?php

namespace App\Modules\Rabet\Services;

use App\Models\Organization;
use App\Models\TrustBadge;
use App\Models\TrustBadgeAward;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TrustBadgeService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function award(Organization $organization, TrustBadge $badge, Model $subject, ?User $awardedBy = null, array $attributes = []): TrustBadgeAward
    {
        if ($badge->status !== 'active') {
            throw ValidationException::withMessages([
                'trust_badge_id' => 'Only active trust badges can be awarded.',
            ]);
        }

        $this->assertTenantMatch($organization, $subject);

        return TrustBadgeAward::query()->updateOrCreate(
            [
                'trust_badge_id' => $badge->id,
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
            ],
            [
                ...$attributes,
                'organization_id' => $organization->id,
                'site_id' => $subject->getAttribute('site_id'),
                'awarded_by_user_id' => $awardedBy?->id,
                'status' => 'active',
                'awarded_at' => now(),
            ],
        );
    }

    public function revoke(TrustBadgeAward $award, ?string $reason = null): TrustBadgeAward
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

    private function assertTenantMatch(Organization $organization, Model $subject): void
    {
        if (! $subject->offsetExists('organization_id')) {
            throw ValidationException::withMessages([
                'subject' => 'Trust badges require a tenant-scoped subject.',
            ]);
        }

        if ((int) $subject->getAttribute('organization_id') !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'subject' => 'The badge subject does not belong to this organization.',
            ]);
        }
    }
}
