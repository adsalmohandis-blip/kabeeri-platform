<?php

namespace Tests\Feature;

use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use App\Modules\Core\Actions\InstallOfficialPackage;
use Database\Seeders\RolesSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OfficialPackageInstallerBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_install_active_official_package(): void
    {
        $this->seed(RolesSeeder::class);

        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $user->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'membership_type' => 'owner',
        ]);
        $role = Role::query()->where('slug', 'organization-owner')->firstOrFail();
        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $user->id,
        ]);
        $package = Package::factory()->create([
            'publisher_type' => 'official',
            'status' => 'active',
        ]);

        $installation = app(InstallOfficialPackage::class)($user, $package, $organization);

        $this->assertSame($package->id, $installation->package_id);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'package.installed',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_unauthorized_user_cannot_install_package(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $package = Package::factory()->create();

        $this->expectException(AuthorizationException::class);

        app(InstallOfficialPackage::class)($user, $package, $organization);
    }

    public function test_non_official_package_cannot_be_installed(): void
    {
        $this->seed(RolesSeeder::class);

        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $user->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
        $role = Role::query()->where('slug', 'organization-owner')->firstOrFail();
        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $user->id,
        ]);
        $package = Package::factory()->create(['publisher_type' => 'third_party']);

        $this->expectException(ValidationException::class);

        app(InstallOfficialPackage::class)($user, $package, $organization);
    }
}
