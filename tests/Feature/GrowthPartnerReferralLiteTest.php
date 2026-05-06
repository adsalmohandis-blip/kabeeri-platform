<?php

namespace Tests\Feature;

use App\Models\GrowthReferral;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Rabet\Services\GrowthReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GrowthPartnerReferralLiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_growth_referral_can_be_created_without_payouts(): void
    {
        $organization = Organization::factory()->create();
        $referrer = User::factory()->create();

        $referral = app(GrowthReferralService::class)->create($organization, $referrer, [
            'code' => 'REF-KABEERI',
            'referred_email' => 'lead@example.test',
            'source' => 'partner_link',
        ]);

        $this->assertDatabaseHas('growth_referrals', [
            'id' => $referral->id,
            'organization_id' => $organization->id,
            'referrer_user_id' => $referrer->id,
            'code' => 'REF-KABEERI',
            'status' => 'pending',
        ]);
        $this->assertArrayNotHasKey('payout', $referral->metadata ?? []);
    }

    public function test_pending_referral_can_be_accepted_for_referred_organization(): void
    {
        $referral = GrowthReferral::factory()->create(['status' => 'pending']);
        $referredOrganization = Organization::factory()->create();

        $accepted = app(GrowthReferralService::class)->accept($referral, $referredOrganization);

        $this->assertSame('accepted', $accepted->status);
        $this->assertSame($referredOrganization->id, $accepted->referred_organization_id);
        $this->assertNotNull($accepted->accepted_at);
    }

    public function test_non_pending_referral_cannot_be_accepted(): void
    {
        $referral = GrowthReferral::factory()->create(['status' => 'expired']);

        $this->expectException(ValidationException::class);

        app(GrowthReferralService::class)->accept($referral, Organization::factory()->create());
    }

    public function test_referral_can_expire(): void
    {
        $referral = GrowthReferral::factory()->create(['status' => 'pending']);

        $expired = app(GrowthReferralService::class)->expire($referral);

        $this->assertSame('expired', $expired->status);
        $this->assertNotNull($expired->expired_at);
    }
}
