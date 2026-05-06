<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\LegalPartnerProfile;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Rabet\Services\LegalPartnerProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RabetLegalPartnerNetworkTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_partner_profile_can_be_registered_for_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $profile = app(LegalPartnerProfileService::class)->register($company->organization, $company, $user, [
            'display_name' => 'Cairo Legal Desk',
            'slug' => 'cairo-legal-desk',
            'practice_areas' => ['contracts'],
            'jurisdictions' => ['EG'],
            'languages' => ['ar'],
        ]);

        $this->assertDatabaseHas('legal_partner_profiles', [
            'id' => $profile->id,
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
            'user_id' => $user->id,
            'verification_status' => 'not_submitted',
            'network_status' => 'draft',
        ]);
        $this->assertSame(['contracts'], $profile->practice_areas);
    }

    public function test_legal_partner_must_be_verified_before_activation(): void
    {
        $profile = LegalPartnerProfile::factory()->create();
        $service = app(LegalPartnerProfileService::class);

        $this->expectException(ValidationException::class);

        $service->activate($profile);
    }

    public function test_verified_legal_partner_can_be_activated(): void
    {
        $profile = LegalPartnerProfile::factory()->create();
        $service = app(LegalPartnerProfileService::class);

        $verified = $service->markVerified($profile);
        $active = $service->activate($verified);

        $this->assertSame('verified', $verified->verification_status);
        $this->assertNotNull($verified->verified_at);
        $this->assertSame('active', $active->network_status);
        $this->assertNotNull($active->activated_at);
    }

    public function test_legal_partner_rejects_cross_tenant_company(): void
    {
        $organization = Organization::factory()->create();
        $company = Company::factory()->create();

        $this->expectException(ValidationException::class);

        app(LegalPartnerProfileService::class)->register($organization, $company);
    }
}
