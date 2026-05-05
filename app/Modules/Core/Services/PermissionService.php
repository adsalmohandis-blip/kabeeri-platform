<?php

namespace App\Modules\Core\Services;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\OrganizationMembership;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PermissionService
{
    public function __construct(
        protected MembershipPermissionService $membershipPermissionService,
    ) {}

    public function hasOrganizationPermission(User $user, int $organizationId, string $permissionSlug): bool
    {
        $memberships = OrganizationMembership::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        return $this->checkAnyMembership(
            memberships: $memberships,
            permissionSlug: $permissionSlug,
            scopeType: 'organization',
            scopeId: $organizationId,
        );
    }

    public function hasCompanyPermission(User $user, int $companyId, string $permissionSlug): bool
    {
        $company = Company::query()->findOrFail($companyId);

        $companyMemberships = CompanyMembership::query()
            ->where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        if ($this->checkAnyMembership($companyMemberships, $permissionSlug, 'company', $companyId)) {
            return true;
        }

        return $this->hasOrganizationPermission($user, $company->organization_id, $permissionSlug);
    }

    public function hasSitePermission(User $user, int $siteId, string $permissionSlug): bool
    {
        $site = Site::query()->findOrFail($siteId);

        $organizationMemberships = OrganizationMembership::query()
            ->where('organization_id', $site->organization_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        if ($this->checkAnyMembership($organizationMemberships, $permissionSlug, 'site', $siteId)) {
            return true;
        }

        if ($site->company_id === null) {
            return false;
        }

        $companyMemberships = CompanyMembership::query()
            ->where('company_id', $site->company_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        return $this->checkAnyMembership($companyMemberships, $permissionSlug, 'site', $siteId);
    }

    public function hasPermission(
        User $user,
        string $permissionSlug,
        string $contextType,
        int $contextId,
    ): bool {
        return match ($contextType) {
            'organization' => $this->hasOrganizationPermission($user, $contextId, $permissionSlug),
            'company' => $this->hasCompanyPermission($user, $contextId, $permissionSlug),
            'site' => $this->hasSitePermission($user, $contextId, $permissionSlug),
            default => false,
        };
    }

    /**
     * @param  Collection<int, Model>  $memberships
     */
    protected function checkAnyMembership(
        Collection $memberships,
        string $permissionSlug,
        string $scopeType,
        int $scopeId,
    ): bool {
        foreach ($memberships as $membership) {
            if ($this->membershipPermissionService->hasPermission(
                membership: $membership,
                permissionSlug: $permissionSlug,
                scopeType: $scopeType,
                scopeId: $scopeId,
            )) {
                return true;
            }
        }

        return false;
    }
}
