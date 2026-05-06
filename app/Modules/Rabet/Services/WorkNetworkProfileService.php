<?php

namespace App\Modules\Rabet\Services;

use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkNetworkProfile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkNetworkProfileService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, ?User $user = null, ?EmployeeProfile $employee = null, array $attributes = []): WorkNetworkProfile
    {
        if ($employee !== null && (int) $employee->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'employee_profile_id' => 'The selected employee profile does not belong to this organization.',
            ]);
        }

        $displayName = $attributes['display_name'] ?? $employee?->full_name ?? $user?->name ?? 'Work Profile';

        return WorkNetworkProfile::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'user_id' => $user?->id ?? $employee?->user_id,
            'employee_profile_id' => $employee?->id,
            'display_name' => $displayName,
            'slug' => $attributes['slug'] ?? Str::slug($displayName),
            'visibility' => 'private',
            'status' => 'draft',
            'availability_status' => 'not_listed',
        ]);
    }

    public function publish(WorkNetworkProfile $profile, string $availabilityStatus = 'available'): WorkNetworkProfile
    {
        $profile->forceFill([
            'visibility' => 'public',
            'status' => 'published',
            'availability_status' => $availabilityStatus,
            'published_at' => now(),
        ])->save();

        return $profile->refresh();
    }

    public function archive(WorkNetworkProfile $profile): WorkNetworkProfile
    {
        $profile->forceFill([
            'visibility' => 'private',
            'status' => 'archived',
            'availability_status' => 'not_listed',
        ])->save();

        return $profile->refresh();
    }
}
