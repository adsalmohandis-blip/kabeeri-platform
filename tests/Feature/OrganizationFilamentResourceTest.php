<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationFilamentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_organization_filament_pages(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create([
            'owner_user_id' => $owner->id,
        ]);

        $this->actingAs($owner);

        $this->get(route('filament.admin.resources.organizations.index'))
            ->assertOk();
        $this->get(route('filament.admin.resources.organizations.view', ['record' => $organization]))
            ->assertOk();
        $this->get(route('filament.admin.resources.organizations.edit', ['record' => $organization]))
            ->assertOk();
    }

    public function test_non_member_cannot_access_other_organization_pages(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $organization = Organization::factory()->create([
            'owner_user_id' => $owner->id,
        ]);

        $this->actingAs($outsider);

        $viewResponse = $this->get(route('filament.admin.resources.organizations.view', ['record' => $organization]));
        $editResponse = $this->get(route('filament.admin.resources.organizations.edit', ['record' => $organization]));

        $this->assertContains($viewResponse->getStatusCode(), [403, 404]);
        $this->assertContains($editResponse->getStatusCode(), [403, 404]);
    }

    public function test_active_member_can_view_but_not_edit_organization(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create([
            'owner_user_id' => $owner->id,
        ]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'membership_type' => 'employee',
            'status' => 'active',
        ]);

        $this->actingAs($member);

        $this->get(route('filament.admin.resources.organizations.view', ['record' => $organization]))
            ->assertOk();

        $editResponse = $this->get(route('filament.admin.resources.organizations.edit', ['record' => $organization]));
        $this->assertContains($editResponse->getStatusCode(), [403, 404]);
    }
}
