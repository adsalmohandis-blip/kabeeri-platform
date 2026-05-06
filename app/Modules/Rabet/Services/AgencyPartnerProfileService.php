<?php

namespace App\Modules\Rabet\Services;

use App\Models\AgencyPartnerProfile;
use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgencyPartnerProfileService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function register(Organization $organization, ?Company $company = null, ?User $user = null, array $attributes = []): AgencyPartnerProfile
    {
        if ($company !== null && (int) $company->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'company_id' => 'The selected company does not belong to this organization.',
            ]);
        }

        $displayName = $attributes['display_name'] ?? $company?->trade_name ?? 'Agency Partner';

        return AgencyPartnerProfile::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $company?->id,
            'user_id' => $user?->id,
            'display_name' => $displayName,
            'slug' => $attributes['slug'] ?? Str::slug($displayName),
            'agency_type' => $attributes['agency_type'] ?? 'implementation_partner',
            'status' => 'draft',
            'accreditation_status' => 'not_submitted',
        ]);
    }

    public function submit(AgencyPartnerProfile $profile): AgencyPartnerProfile
    {
        $profile->forceFill([
            'status' => 'submitted',
            'accreditation_status' => 'submitted',
            'submitted_at' => now(),
        ])->save();

        return $profile->refresh();
    }

    public function accredit(AgencyPartnerProfile $profile, string $level = 'standard'): AgencyPartnerProfile
    {
        if ($profile->accreditation_status !== 'submitted') {
            throw ValidationException::withMessages([
                'accreditation_status' => 'Agency partner must submit accreditation before approval.',
            ]);
        }

        $profile->forceFill([
            'status' => 'active',
            'accreditation_status' => 'accredited',
            'accreditation_level' => $level,
            'accredited_at' => now(),
        ])->save();

        return $profile->refresh();
    }
}
