<?php

namespace App\Policies;

use App\Models\ContentType;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class ContentTypePolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, ContentType $contentType): bool
    {
        if ($contentType->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $contentType->site_id, 'content.view')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $contentType->organization_id, 'content.view')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentType->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, ContentType $contentType): bool
    {
        if ($contentType->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $contentType->site_id, 'content.edit')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $contentType->organization_id, 'content.edit')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentType->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function delete(User $user, ContentType $contentType): bool
    {
        if ($contentType->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $contentType->site_id, 'content.delete')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $contentType->organization_id, 'content.delete')) {
            return true;
        }

        return Organization::query()
            ->where('id', $contentType->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }
}
