<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyMembershipFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_belong_to_multiple_companies_through_memberships(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $companyA = Company::factory()->create(['organization_id' => $organization->id]);
        $companyB = Company::factory()->create(['organization_id' => $organization->id]);

        CompanyMembership::factory()->create([
            'company_id' => $companyA->id,
            'user_id' => $user->id,
        ]);

        CompanyMembership::factory()->create([
            'company_id' => $companyB->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(2, $user->companyMemberships);
        $this->assertCount(2, $user->companies);
    }

    public function test_company_membership_is_unique_per_company_and_user(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        CompanyMembership::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        $this->expectException(QueryException::class);

        CompanyMembership::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_company_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $company = Company::factory()->create(['organization_id' => $organization->id]);

        $this->assertTrue($company->organization->is($organization));
    }
}
