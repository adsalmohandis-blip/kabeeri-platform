<?php

namespace App\Policies;

use App\Models\ContentEntry;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class ContentEntryPolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, ContentEntry $contentEntry): bool
    {
        if ($this->permissionService->hasSitePermission($user, $contentEntry->site_id, 'content.view')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentEntry->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function createForSite(User $user, Site $site): bool
    {
        if ($this->permissionService->hasSitePermission($user, $site->id, 'content.create')) {
            return true;
        }

        return Organization::query()
            ->where('id', $site->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function update(User $user, ContentEntry $contentEntry): bool
    {
        if ($this->permissionService->hasSitePermission($user, $contentEntry->site_id, 'content.edit')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentEntry->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function publish(User $user, ContentEntry $contentEntry): bool
    {
        if ($this->permissionService->hasSitePermission($user, $contentEntry->site_id, 'content.publish')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentEntry->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function archive(User $user, ContentEntry $contentEntry): bool
    {
        if ($this->permissionService->hasSitePermission($user, $contentEntry->site_id, 'content.delete')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentEntry->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function delete(User $user, ContentEntry $contentEntry): bool
    {
        return $this->archive($user, $contentEntry);
    }
}
