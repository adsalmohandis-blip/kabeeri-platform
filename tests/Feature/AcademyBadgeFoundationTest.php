<?php

namespace Tests\Feature;

use App\Models\AcademyBadge;
use App\Models\AcademyBadgeAward;
use App\Models\Organization;
use App\Models\User;
use App\Models\WorkNetworkProfile;
use App\Modules\Rabet\Services\AcademyBadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AcademyBadgeFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_academy_badge_can_be_awarded_to_work_profile(): void
    {
        $profile = WorkNetworkProfile::factory()->create();
        $badge = AcademyBadge::factory()->create(['key' => 'academy.cms.basic']);
        $awardedBy = User::factory()->create();

        $award = app(AcademyBadgeService::class)->award($profile->organization, $badge, null, $profile, $awardedBy);

        $this->assertDatabaseHas('academy_badge_awards', [
            'id' => $award->id,
            'organization_id' => $profile->organization_id,
            'academy_badge_id' => $badge->id,
            'work_network_profile_id' => $profile->id,
            'awarded_by_user_id' => $awardedBy->id,
            'status' => 'active',
        ]);
        $this->assertNotNull($award->awarded_at);
    }

    public function test_badge_award_can_be_revoked(): void
    {
        $award = AcademyBadgeAward::factory()->create();

        $revoked = app(AcademyBadgeService::class)->revoke($award, 'expired curriculum');

        $this->assertSame('revoked', $revoked->status);
        $this->assertSame('expired curriculum', $revoked->metadata['revoked_reason']);
    }

    public function test_inactive_academy_badge_cannot_be_awarded(): void
    {
        $organization = Organization::factory()->create();
        $badge = AcademyBadge::factory()->create(['status' => 'retired']);

        $this->expectException(ValidationException::class);

        app(AcademyBadgeService::class)->award($organization, $badge, User::factory()->create());
    }

    public function test_award_requires_recipient(): void
    {
        $organization = Organization::factory()->create();
        $badge = AcademyBadge::factory()->create();

        $this->expectException(ValidationException::class);

        app(AcademyBadgeService::class)->award($organization, $badge);
    }
}
