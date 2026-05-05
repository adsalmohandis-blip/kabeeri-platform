<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Modules\Core\Actions\CreateOrganizationWithOwnerMembership;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationMembershipFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_organization_action_creates_owner_membership(): void
    {
        $owner = User::factory()->create();

        $organization = app(CreateOrganizationWithOwnerMembership::class)(
            [
                'name' => 'Kabeeri Org',
                'slug' => 'kabeeri-org',
            ],
            $owner,
        );

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'owner_user_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'membership_type' => 'owner',
            'status' => 'active',
        ]);
    }

    public function test_user_can_belong_to_multiple_organizations(): void
    {
        $user = User::factory()->create();
        $ownerA = User::factory()->create();
        $ownerB = User::factory()->create();
        $organizationA = Organization::factory()->create(['owner_user_id' => $ownerA->id]);
        $organizationB = Organization::factory()->create(['owner_user_id' => $ownerB->id]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organizationA->id,
            'user_id' => $user->id,
            'membership_type' => 'employee',
        ]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organizationB->id,
            'user_id' => $user->id,
            'membership_type' => 'consultant',
        ]);

        $this->assertCount(2, $user->organizationMemberships);
        $this->assertCount(2, $user->organizations);
    }

    public function test_membership_is_unique_per_user_and_organization(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);

        $this->expectException(QueryException::class);

        OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);
    }
}
