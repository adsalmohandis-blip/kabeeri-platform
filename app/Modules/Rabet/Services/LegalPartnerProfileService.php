<?php

namespace App\Modules\Rabet\Services;

use App\Models\Company;
use App\Models\LegalPartnerProfile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LegalPartnerProfileService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Organization $organization, ?Company $company = null, ?User $user = null, array $attributes = []): LegalPartnerProfile
    {
        if ($company !== null && (int) $company->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'company_id' => 'The selected company does not belong to this organization.',
            ]);
        }

        $displayName = $attributes['display_name'] ?? $company?->trade_name ?? 'Legal Partner';

        return LegalPartnerProfile::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $company?->id,
            'user_id' => $user?->id,
            'display_name' => $displayName,
            'slug' => $attributes['slug'] ?? Str::slug($displayName),
            'partner_type' => $attributes['partner_type'] ?? 'legal_consultant',
            'verification_status' => 'not_submitted',
            'network_status' => 'draft',
        ]);
    }

    public function markVerified(LegalPartnerProfile $profile): LegalPartnerProfile
    {
        $profile->forceFill([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ])->save();

        return $profile->refresh();
    }

    public function activate(LegalPartnerProfile $profile): LegalPartnerProfile
    {
        if ($profile->verification_status !== 'verified') {
            throw ValidationException::withMessages([
                'verification_status' => 'Legal partner must be verified before network activation.',
            ]);
        }

        $profile->forceFill([
            'network_status' => 'active',
            'activated_at' => now(),
        ])->save();

        return $profile->refresh();
    }
}
