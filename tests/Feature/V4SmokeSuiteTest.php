<?php

namespace Tests\Feature;

use App\Models\AcademyBadge;
use App\Models\AgencyPartnerProfile;
use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorProduct;
use App\Models\Organization;
use App\Models\Package;
use App\Models\TrustBadge;
use App\Models\User;
use App\Models\WorkNetworkProfile;
use App\Modules\Core\Services\InternalMarketplaceCatalogService;
use App\Modules\Mall\Services\ModerationFlagService;
use App\Modules\Mall\Services\ReputationSnapshotService;
use App\Modules\Mall\Services\ReviewService;
use App\Modules\Rabet\Services\AcademyBadgeService;
use App\Modules\Rabet\Services\AgencyDashboardService;
use App\Modules\Rabet\Services\GrowthReferralService;
use App\Modules\Rabet\Services\PartnerStorefrontService;
use App\Modules\Rabet\Services\TrustBadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V4SmokeSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_mall_smoke_flow_lists_published_mirrors_only(): void
    {
        MallMirrorBusiness::factory()->create([
            'display_name' => 'Smoke Business',
            'slug' => 'smoke-business',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Smoke Product',
            'slug' => 'smoke-product',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Hidden Smoke Product',
            'slug' => 'hidden-smoke-product',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall')
            ->assertOk()
            ->assertSee('Business Directory')
            ->assertSee('Products');

        $this->get('/mall/products')
            ->assertOk()
            ->assertSee('Smoke Product')
            ->assertDontSee('Hidden Smoke Product');
    }

    public function test_trust_and_moderation_smoke_flow_records_review_snapshot_and_badge(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $user = User::factory()->create();

        $flag = app(ModerationFlagService::class)->report($business->organization, $business, [
            'flag_type' => 'smoke_report',
        ]);
        $review = app(ReviewService::class)->submit($business->organization, $business, $user, [
            'rating' => 5,
            'title' => 'Smoke Review',
        ]);
        app(ReviewService::class)->approve($review);
        $snapshot = app(ReputationSnapshotService::class)->calculate($business->organization, $business);
        $badge = TrustBadge::factory()->create(['status' => 'active']);
        $award = app(TrustBadgeService::class)->award($business->organization, $badge, $business, $user);

        $this->assertNotNull($flag->moderation_case_id);
        $this->assertSame(1, $snapshot->published_review_count);
        $this->assertSame('active', $award->status);
    }

    public function test_marketplace_partner_work_academy_and_referral_smoke_flow(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $package = Package::factory()->create();
        $marketplace = app(InternalMarketplaceCatalogService::class);
        $catalogItem = $marketplace->approve($marketplace->register($package), $user);
        $agency = AgencyPartnerProfile::factory()->create([
            'organization_id' => $organization->id,
            'status' => 'active',
            'accreditation_status' => 'accredited',
        ]);
        $dashboard = app(AgencyDashboardService::class)->snapshot($agency);
        $storefront = app(PartnerStorefrontService::class)->createDraft($organization, $agency);
        $share = app(PartnerStorefrontService::class)->shareDraft($storefront, $package);
        $workProfile = WorkNetworkProfile::factory()->create(['organization_id' => $organization->id]);
        $academyBadge = AcademyBadge::factory()->create(['status' => 'active']);
        $academyAward = app(AcademyBadgeService::class)->award($organization, $academyBadge, null, $workProfile, $user);
        $referral = app(GrowthReferralService::class)->create($organization, $user, ['code' => 'SMOKE-REF']);

        $this->assertSame('approved', $catalogItem->governance_status);
        $this->assertSame('accredited', $dashboard->metrics['accreditation_status']);
        $this->assertSame('draft', $share->status);
        $this->assertSame('active', $academyAward->status);
        $this->assertSame('pending', $referral->status);
    }
}
