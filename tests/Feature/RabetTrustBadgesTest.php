<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\Organization;
use App\Models\TrustBadge;
use App\Models\TrustBadgeAward;
use App\Models\User;
use App\Modules\Rabet\Services\TrustBadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RabetTrustBadgesTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_trust_badge_can_be_awarded_to_tenant_subject(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $badge = TrustBadge::factory()->create(['key' => 'verified-business', 'status' => 'active']);
        $awardedBy = User::factory()->create();

        $award = app(TrustBadgeService::class)->award($business->organization, $badge, $business, $awardedBy);

        $this->assertDatabaseHas('trust_badge_awards', [
            'id' => $award->id,
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'trust_badge_id' => $badge->id,
            'subject_type' => $business->getMorphClass(),
            'subject_id' => $business->id,
            'awarded_by_user_id' => $awardedBy->id,
            'status' => 'active',
        ]);
        $this->assertNotNull($award->awarded_at);
    }

    public function test_award_can_be_revoked(): void
    {
        $award = TrustBadgeAward::factory()->create();

        $revoked = app(TrustBadgeService::class)->revoke($award, 'expired verification');

        $this->assertSame('revoked', $revoked->status);
        $this->assertSame('expired verification', $revoked->metadata['revoked_reason']);
    }

    public function test_inactive_badge_cannot_be_awarded(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $badge = TrustBadge::factory()->create(['status' => 'retired']);

        $this->expectException(ValidationException::class);

        app(TrustBadgeService::class)->award($business->organization, $badge, $business);
    }

    public function test_badge_award_rejects_cross_tenant_subject(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $badge = TrustBadge::factory()->create();

        $this->expectException(ValidationException::class);

        app(TrustBadgeService::class)->award(Organization::factory()->create(), $badge, $business);
    }
}
