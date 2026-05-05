<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;

class SettingPolicy
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Setting $setting): bool
    {
        return $this->canAccessSetting($user, $setting);
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Setting $setting): bool
    {
        if ($setting->scope_type === 'platform') {
            return false;
        }

        return $this->canManageSetting($user, $setting);
    }

    public function delete(User $user, Setting $setting): bool
    {
        if ($setting->scope_type === 'platform') {
            return false;
        }

        return $this->canManageSetting($user, $setting);
    }

    protected function canAccessSetting(User $user, Setting $setting): bool
    {
        return $setting->scope_type !== 'platform' && $this->resolveScopeAccess($user, $setting, false);
    }

    protected function canManageSetting(User $user, Setting $setting): bool
    {
        return $this->resolveScopeAccess($user, $setting, true);
    }

    protected function resolveScopeAccess(User $user, Setting $setting, bool $forManage): bool
    {
        if ($setting->scope_type === 'organization') {
            $organizationId = $setting->scope_id ?? $setting->organization_id;

            if ($organizationId === null) {
                return false;
            }

            if ($forManage && $this->permissionService->hasOrganizationPermission($user, $organizationId, 'organization.settings.manage')) {
                return true;
            }

            return Organization::query()
                ->where('id', $organizationId)
                ->where(function ($query) use ($user): void {
                    $query->where('owner_user_id', $user->id)
                        ->orWhereHas('memberships', function ($membershipQuery) use ($user): void {
                            $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                        });
                })
                ->exists();
        }

        if ($setting->scope_type === 'site' && $setting->scope_id !== null) {
            if ($forManage) {
                if ($this->permissionService->hasSitePermission($user, $setting->scope_id, 'site.settings.manage')) {
                    return true;
                }
            } elseif ($this->permissionService->hasSitePermission($user, $setting->scope_id, 'site.view')) {
                return true;
            }

            return Site::query()
                ->where('id', $setting->scope_id)
                ->whereHas('organization', function ($organizationQuery) use ($user): void {
                    $organizationQuery
                        ->where('owner_user_id', $user->id)
                        ->orWhereHas('memberships', function ($membershipQuery) use ($user): void {
                            $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                        });
                })
                ->exists();
        }

        if ($setting->scope_type === 'company' && $setting->scope_id !== null) {
            if ($forManage) {
                if ($this->permissionService->hasCompanyPermission($user, $setting->scope_id, 'company.manage')) {
                    return true;
                }
            } elseif ($this->permissionService->hasCompanyPermission($user, $setting->scope_id, 'company.view')) {
                return true;
            }

            return Company::query()
                ->where('id', $setting->scope_id)
                ->whereHas('organization', function ($organizationQuery) use ($user): void {
                    $organizationQuery
                        ->where('owner_user_id', $user->id)
                        ->orWhereHas('memberships', function ($membershipQuery) use ($user): void {
                            $membershipQuery->where('user_id', $user->id)->where('status', 'active');
                        });
                })
                ->exists();
        }

        return false;
    }
}
