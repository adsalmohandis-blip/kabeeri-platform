<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class ActivityLogPolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        if ($activityLog->organization_id === null) {
            return false;
        }

        if ($this->permissionService->hasOrganizationPermission($user, $activityLog->organization_id, 'activity_log.view')) {
            return true;
        }

        return Organization::query()
            ->where('id', $activityLog->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }
}
