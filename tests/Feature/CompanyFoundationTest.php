<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use App\Policies\CompanyPolicy;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_be_created_under_organization(): void
    {
        $organization = Organization::factory()->create();

        $company = Company::factory()->create([
            'organization_id' => $organization->id,
            'trade_name' => 'Kabeeri Clinic',
            'slug' => 'kabeeri-clinic',
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'organization_id' => $organization->id,
            'slug' => 'kabeeri-clinic',
        ]);

        $this->assertTrue($company->organization->is($organization));
        $this->assertCount(1, $organization->companies);
    }

    public function test_company_slug_is_unique_within_organization(): void
    {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        Company::factory()->create([
            'organization_id' => $organizationA->id,
            'slug' => 'demo-company',
        ]);

        Company::factory()->create([
            'organization_id' => $organizationB->id,
            'slug' => 'demo-company',
        ]);

        $this->expectException(QueryException::class);

        Company::factory()->create([
            'organization_id' => $organizationA->id,
            'slug' => 'demo-company',
        ]);
    }

    public function test_only_organization_owner_can_update_company_via_policy(): void
    {
        $owner = User::factory()->create();
        $nonOwner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $policy = new CompanyPolicy;

        $this->assertTrue($policy->update($owner, $company));
        $this->assertFalse($policy->update($nonOwner, $company));
    }
}
