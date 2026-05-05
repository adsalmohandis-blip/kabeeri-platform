<?php

namespace App\Policies;

use App\Models\FeatureFlagOverride;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class FeatureFlagOverridePolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, FeatureFlagOverride $override): bool
    {
        if ($override->organization_id === null) {
            return false;
        }

        return $this->canManageOrganizationFlags($user, $override->organization_id);
    }

    public function create(User $user): bool
    {
        if (! $user->exists) {
            return false;
        }

        $ownedOrganizationIds = Organization::query()
            ->where('owner_user_id', $user->id)
            ->pluck('id')
            ->all();

        if ($ownedOrganizationIds !== []) {
            return true;
        }

        $membershipOrganizationIds = Organization::query()
            ->whereHas('memberships', function ($query) use ($user): void {
                $query->where('user_id', $user->id)->where('status', 'active');
            })
            ->pluck('id')
            ->all();

        foreach ($membershipOrganizationIds as $organizationId) {
            if ($this->permissionService->hasOrganizationPermission($user, (int) $organizationId, 'organization.settings.manage')) {
                return true;
            }
        }

        return false;
    }

    public function update(User $user, FeatureFlagOverride $override): bool
    {
        if ($override->organization_id === null) {
            return false;
        }

        return $this->canManageOrganizationFlags($user, $override->organization_id);
    }

    public function delete(User $user, FeatureFlagOverride $override): bool
    {
        if ($override->organization_id === null) {
            return false;
        }

        return $this->canManageOrganizationFlags($user, $override->organization_id);
    }

    protected function canManageOrganizationFlags(User $user, int $organizationId): bool
    {
        if ($this->permissionService->hasOrganizationPermission($user, $organizationId, 'organization.settings.manage')) {
            return true;
        }

        return Organization::query()
            ->where('id', $organizationId)
            ->where('owner_user_id', $user->id)
            ->exists();
    }
}
