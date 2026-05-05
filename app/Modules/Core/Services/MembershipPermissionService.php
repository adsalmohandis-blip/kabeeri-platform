<?php

namespace App\Modules\Core\Services;

use App\Models\MembershipPermissionOverride;
use App\Models\MembershipRoleAssignment;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class MembershipPermissionService
{
    public function hasPermission(
        Model $membership,
        string $permissionSlug,
        ?string $scopeType = null,
        ?int $scopeId = null,
    ): bool {
        return $this->hasPermissionForScope($membership, $permissionSlug, $scopeType, $scopeId);
    }

    public function hasPermissionForScope(
        Model $membership,
        string $permissionSlug,
        ?string $scopeType = null,
        ?int $scopeId = null,
    ): bool {
        $permission = Permission::query()->where('slug', $permissionSlug)->first();

        if (! $permission) {
            return false;
        }

        $membershipType = $membership->getMorphClass();
        $membershipId = $membership->getKey();

        $override = MembershipPermissionOverride::query()
            ->where('membership_type', $membershipType)
            ->where('membership_id', $membershipId)
            ->where('permission_id', $permission->id)
            ->orderByDesc('created_at')
            ->first();

        if ($override) {
            return $override->effect === 'allow';
        }

        $roleAssignmentsQuery = MembershipRoleAssignment::query()
            ->where('membership_type', $membershipType)
            ->where('membership_id', $membershipId)
            ->whereHas('role.permissions', function ($query) use ($permission): void {
                $query->where('permissions.id', $permission->id);
            });

        if ($scopeType !== null && $scopeId !== null) {
            $roleAssignmentsQuery->where(function ($query) use ($scopeType, $scopeId): void {
                $query->whereNull('scope_type')
                    ->whereNull('scope_id')
                    ->orWhere(function ($scopedQuery) use ($scopeType, $scopeId): void {
                        $scopedQuery
                            ->where('scope_type', $scopeType)
                            ->where('scope_id', $scopeId);
                    });
            });
        }

        return $roleAssignmentsQuery->exists();
    }
}
