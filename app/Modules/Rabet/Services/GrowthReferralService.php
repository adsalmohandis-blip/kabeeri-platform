<?php

namespace App\Modules\Rabet\Services;

use App\Models\GrowthReferral;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GrowthReferralService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, ?User $referrer = null, array $attributes = []): GrowthReferral
    {
        return GrowthReferral::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'referrer_user_id' => $referrer?->id,
            'code' => $attributes['code'] ?? Str::upper('REF-'.Str::random(8)),
            'source' => $attributes['source'] ?? 'manual',
            'status' => 'pending',
        ]);
    }

    public function accept(GrowthReferral $referral, Organization $referredOrganization): GrowthReferral
    {
        if ($referral->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending referrals can be accepted.',
            ]);
        }

        $referral->forceFill([
            'status' => 'accepted',
            'referred_organization_id' => $referredOrganization->id,
            'accepted_at' => now(),
        ])->save();

        return $referral->refresh();
    }

    public function expire(GrowthReferral $referral): GrowthReferral
    {
        $referral->forceFill([
            'status' => 'expired',
            'expired_at' => now(),
        ])->save();

        return $referral->refresh();
    }
}
