<?php

namespace App\Modules\Core\Services;

use App\Models\Company;
use App\Models\CreatorProfile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatorProfileService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, ?User $user = null, ?Company $company = null, array $attributes = []): CreatorProfile
    {
        if ($company !== null && (int) $company->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'company_id' => 'The selected company does not belong to this organization.',
            ]);
        }

        $displayName = $attributes['display_name'] ?? $company?->trade_name ?? $user?->name ?? 'Creator Profile';

        return CreatorProfile::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $company?->id,
            'user_id' => $user?->id,
            'display_name' => $displayName,
            'slug' => $attributes['slug'] ?? Str::slug($displayName),
            'profile_type' => $attributes['profile_type'] ?? 'creator',
            'status' => 'draft',
            'verification_status' => $attributes['verification_status'] ?? 'not_submitted',
        ]);
    }

    public function submit(CreatorProfile $profile): CreatorProfile
    {
        $profile->forceFill([
            'status' => 'submitted',
            'submitted_at' => now(),
        ])->save();

        return $profile->refresh();
    }

    public function approve(CreatorProfile $profile): CreatorProfile
    {
        if ($profile->status !== 'submitted') {
            throw ValidationException::withMessages([
                'status' => 'Creator profile must be submitted before approval.',
            ]);
        }

        $profile->forceFill([
            'status' => 'active',
            'approved_at' => now(),
        ])->save();

        return $profile->refresh();
    }
}
