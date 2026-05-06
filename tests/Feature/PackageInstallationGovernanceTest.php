<?php

namespace Tests\Feature;

use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use App\Modules\Core\Actions\InstallOfficialPackage;
use App\Modules\Core\Services\InternalMarketplaceCatalogService;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PackageInstallationGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_with_pending_marketplace_governance_cannot_be_installed(): void
    {
        [$user, $organization] = $this->authorizedInstaller();
        $package = Package::factory()->create();

        app(InternalMarketplaceCatalogService::class)->register($package);

        $this->expectException(ValidationException::class);

        app(InstallOfficialPackage::class)($user, $package, $organization);
    }

    public function test_approved_marketplace_package_can_be_installed(): void
    {
        [$user, $organization] = $this->authorizedInstaller();
        $package = Package::factory()->create();
        $catalog = app(InternalMarketplaceCatalogService::class);

        $catalog->approve($catalog->register($package), $user);

        $installation = app(InstallOfficialPackage::class)($user, $package, $organization);

        $this->assertSame($package->id, $installation->package_id);
    }

    public function test_package_with_missing_dependency_cannot_be_installed(): void
    {
        [$user, $organization] = $this->authorizedInstaller();
        $package = Package::factory()->create([
            'dependencies' => ['kabeeri.missing'],
        ]);

        $this->expectException(ValidationException::class);

        app(InstallOfficialPackage::class)($user, $package, $organization);
    }

    /**
     * @return array{0: User, 1: Organization}
     */
    private function authorizedInstaller(): array
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

        return [$user, $organization];
    }
}
