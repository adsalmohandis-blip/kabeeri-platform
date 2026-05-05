<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Policies\OrganizationPolicy;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_belongs_to_owner_and_owner_has_many_owned_organizations(): void
    {
        $owner = User::factory()->create();
        $organizationA = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $organizationB = Organization::factory()->create(['owner_user_id' => $owner->id]);

        $this->assertTrue($organizationA->owner->is($owner));
        $this->assertTrue($organizationB->owner->is($owner));
        $this->assertCount(2, $owner->ownedOrganizations);
    }

    public function test_organization_slug_must_be_unique(): void
    {
        $owner = User::factory()->create();

        Organization::factory()->create([
            'owner_user_id' => $owner->id,
            'slug' => 'kabeeri-demo',
        ]);

        $this->expectException(QueryException::class);

        Organization::factory()->create([
            'owner_user_id' => $owner->id,
            'slug' => 'kabeeri-demo',
        ]);
    }

    public function test_only_owner_can_update_organization_via_policy(): void
    {
        $owner = User::factory()->create();
        $nonOwner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $policy = new OrganizationPolicy;

        $this->assertTrue($policy->update($owner, $organization));
        $this->assertFalse($policy->update($nonOwner, $organization));
    }
}
