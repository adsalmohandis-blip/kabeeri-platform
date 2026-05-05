<?php

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class MediaAssetPolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasPermissionForContext($user, $mediaAsset, 'media.view');
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasPermissionForContext($user, $mediaAsset, 'media.edit');
    }

    public function delete(User $user, MediaAsset $mediaAsset): bool
    {
        return $this->hasPermissionForContext($user, $mediaAsset, 'media.delete');
    }

    protected function hasPermissionForContext(User $user, MediaAsset $mediaAsset, string $permission): bool
    {
        if ($mediaAsset->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $mediaAsset->site_id, $permission)) {
                return true;
            }
        }

        if ($mediaAsset->company_id !== null) {
            if ($this->permissionService->hasCompanyPermission($user, $mediaAsset->company_id, $permission)) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $mediaAsset->organization_id, $permission)) {
            return true;
        }

        return Organization::query()
            ->where('id', $mediaAsset->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }
}
