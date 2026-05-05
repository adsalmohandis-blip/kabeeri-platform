<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\MembershipPermissionOverride;
use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\PermissionService;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_owner_role_can_manage_organization(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'membership_type' => 'owner',
        ]);

        $role = Role::query()->where('slug', 'organization-owner')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        $service = app(PermissionService::class);

        $this->assertTrue($service->hasOrganizationPermission($owner, $organization->id, 'organization.manage'));
    }

    public function test_user_without_permission_cannot_manage_organization(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $memberUser = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $memberUser->id,
            'membership_type' => 'employee',
        ]);

        $role = Role::query()->where('slug', 'viewer')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        $service = app(PermissionService::class);

        $this->assertFalse($service->hasOrganizationPermission($memberUser, $organization->id, 'organization.manage'));
    }

    public function test_permission_override_deny_wins_over_role_allow(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'membership_type' => 'owner',
        ]);

        $role = Role::query()->where('slug', 'organization-owner')->firstOrFail();
        $permission = Permission::query()->where('slug', 'organization.manage')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        MembershipPermissionOverride::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'permission_id' => $permission->id,
            'effect' => 'deny',
            'reason' => 'Temporary lock',
            'created_by' => $owner->id,
        ]);

        $service = app(PermissionService::class);

        $this->assertFalse($service->hasOrganizationPermission($owner, $organization->id, 'organization.manage'));
    }

    public function test_permission_service_supports_company_and_site_contexts(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $organizationMembership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'membership_type' => 'owner',
        ]);
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->forCompany($company)->create();

        $siteAdminRole = Role::query()->where('slug', 'site-admin')->firstOrFail();
        $organizationOwnerRole = Role::query()->where('slug', 'organization-owner')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $organizationMembership->getMorphClass(),
            'membership_id' => $organizationMembership->id,
            'role_id' => $siteAdminRole->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'created_by' => $owner->id,
        ]);

        MembershipRoleAssignment::query()->create([
            'membership_type' => $organizationMembership->getMorphClass(),
            'membership_id' => $organizationMembership->id,
            'role_id' => $organizationOwnerRole->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        $companyMembership = CompanyMembership::factory()->create([
            'company_id' => $company->id,
            'user_id' => $owner->id,
            'organization_membership_id' => $organizationMembership->id,
        ]);

        MembershipRoleAssignment::query()->create([
            'membership_type' => $companyMembership->getMorphClass(),
            'membership_id' => $companyMembership->id,
            'role_id' => $siteAdminRole->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'created_by' => $owner->id,
        ]);

        $service = app(PermissionService::class);

        $this->assertTrue($service->hasCompanyPermission($owner, $company->id, 'company.manage'));
        $this->assertTrue($service->hasSitePermission($owner, $site->id, 'site.edit'));
    }
}
