<?php

namespace Tests\Feature;

use App\Models\AgencyPartnerProfile;
use App\Models\Company;
use App\Models\Organization;
use App\Modules\Rabet\Services\AgencyPartnerProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AgencyPartnerAccreditationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_partner_can_be_registered_for_company(): void
    {
        $company = Company::factory()->create();

        $profile = app(AgencyPartnerProfileService::class)->register($company->organization, $company, null, [
            'display_name' => 'Kabeeri Launch Agency',
            'slug' => 'kabeeri-launch-agency',
            'service_categories' => ['implementation'],
            'regions' => ['EG', 'SA'],
        ]);

        $this->assertDatabaseHas('agency_partner_profiles', [
            'id' => $profile->id,
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
            'status' => 'draft',
            'accreditation_status' => 'not_submitted',
        ]);
        $this->assertSame(['implementation'], $profile->service_categories);
    }

    public function test_agency_partner_can_submit_and_receive_accreditation(): void
    {
        $profile = AgencyPartnerProfile::factory()->create(['status' => 'draft']);
        $service = app(AgencyPartnerProfileService::class);

        $submitted = $service->submit($profile);

        $this->assertSame('submitted', $submitted->status);
        $this->assertSame('submitted', $submitted->accreditation_status);

        $accredited = $service->accredit($submitted, 'gold');

        $this->assertSame('active', $accredited->status);
        $this->assertSame('accredited', $accredited->accreditation_status);
        $this->assertSame('gold', $accredited->accreditation_level);
        $this->assertNotNull($accredited->accredited_at);
    }

    public function test_accreditation_requires_submission(): void
    {
        $profile = AgencyPartnerProfile::factory()->create(['accreditation_status' => 'not_submitted']);

        $this->expectException(ValidationException::class);

        app(AgencyPartnerProfileService::class)->accredit($profile);
    }

    public function test_agency_partner_rejects_cross_tenant_company(): void
    {
        $organization = Organization::factory()->create();
        $company = Company::factory()->create();

        $this->expectException(ValidationException::class);

        app(AgencyPartnerProfileService::class)->register($organization, $company);
    }
}
