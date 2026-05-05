<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Taxonomy;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class TaxonomyPolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Taxonomy $taxonomy): bool
    {
        if ($taxonomy->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $taxonomy->site_id, 'content.view')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $taxonomy->organization_id, 'content.view')) {
            return true;
        }

        return Organization::query()
            ->where('id', $taxonomy->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Taxonomy $taxonomy): bool
    {
        if ($taxonomy->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $taxonomy->site_id, 'content.edit')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $taxonomy->organization_id, 'content.edit')) {
            return true;
        }

        return Organization::query()
            ->where('id', $taxonomy->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }

    public function delete(User $user, Taxonomy $taxonomy): bool
    {
        if ($taxonomy->site_id !== null) {
            if ($this->permissionService->hasSitePermission($user, $taxonomy->site_id, 'content.delete')) {
                return true;
            }
        }

        if ($this->permissionService->hasOrganizationPermission($user, $taxonomy->organization_id, 'content.delete')) {
            return true;
        }

        return Organization::query()
            ->where('id', $taxonomy->organization_id)
            ->where('owner_user_id', $user->id)
            ->exists();
    }
}
