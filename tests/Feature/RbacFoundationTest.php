<?php

namespace Tests\Feature;

use App\Models\MembershipPermissionOverride;
use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Modules\Core\Services\MembershipPermissionService;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_seeder_inserts_v1_permissions(): void
    {
        $this->seed(PermissionsSeeder::class);

        $this->assertDatabaseHas('permissions', ['slug' => 'organization.view']);
        $this->assertDatabaseHas('permissions', ['slug' => 'site.create']);
        $this->assertDatabaseHas('permissions', ['slug' => 'content.create']);
        $this->assertDatabaseHas('permissions', ['slug' => 'activity_log.view']);
    }

    public function test_can_assign_role_to_organization_membership_and_check_permission(): void
    {
        $this->seed(PermissionsSeeder::class);

        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'membership_type' => 'employee',
        ]);

        $role = Role::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Content Editor',
            'slug' => 'content-editor',
            'scope' => 'organization',
            'is_system' => false,
        ]);

        $permission = Permission::query()->where('slug', 'content.create')->firstOrFail();

        $role->permissions()->attach($permission->id);

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        $service = app(MembershipPermissionService::class);

        $this->assertTrue($service->hasPermission($membership, 'content.create'));
        $this->assertFalse($service->hasPermission($membership, 'role.manage'));
    }

    public function test_membership_permission_override_can_deny_a_role_permission(): void
    {
        $this->seed(PermissionsSeeder::class);

        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'membership_type' => 'employee',
        ]);

        $role = Role::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Media Manager',
            'slug' => 'media-manager',
            'scope' => 'organization',
            'is_system' => false,
        ]);

        $permission = Permission::query()->where('slug', 'media.upload')->firstOrFail();

        $role->permissions()->attach($permission->id);

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
            'reason' => 'Temporarily restricted',
            'created_by' => $owner->id,
        ]);

        $service = app(MembershipPermissionService::class);

        $this->assertFalse($service->hasPermission($membership, 'media.upload'));
    }
}
