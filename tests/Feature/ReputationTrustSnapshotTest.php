<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\ModerationCase;
use App\Models\Organization;
use App\Models\Review;
use App\Modules\Mall\Services\ReputationSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReputationTrustSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_reputation_snapshot_calculates_basic_trust_metrics(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        Review::factory()->create([
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'reviewable_type' => $business->getMorphClass(),
            'reviewable_id' => $business->id,
            'rating' => 5,
            'status' => 'published',
            'published_at' => now(),
        ]);
        Review::factory()->create([
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'reviewable_type' => $business->getMorphClass(),
            'reviewable_id' => $business->id,
            'rating' => 3,
            'status' => 'published',
            'published_at' => now(),
        ]);
        Review::factory()->create([
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'reviewable_type' => $business->getMorphClass(),
            'reviewable_id' => $business->id,
            'rating' => 1,
            'status' => 'pending',
        ]);
        ModerationCase::factory()->create([
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'subject_type' => $business->getMorphClass(),
            'subject_id' => $business->id,
            'status' => 'open',
        ]);

        $snapshot = app(ReputationSnapshotService::class)->calculate($business->organization, $business);

        $this->assertSame(3, $snapshot->review_count);
        $this->assertSame(2, $snapshot->published_review_count);
        $this->assertSame('4.00', $snapshot->average_rating);
        $this->assertSame(1, $snapshot->open_moderation_cases_count);
        $this->assertSame(72, $snapshot->trust_score);
    }

    public function test_reputation_snapshot_updates_existing_subject_snapshot(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $service = app(ReputationSnapshotService::class);

        $first = $service->calculate($business->organization, $business);
        $second = $service->calculate($business->organization, $business);

        $this->assertSame($first->id, $second->id);
    }

    public function test_reputation_snapshot_rejects_cross_tenant_subject(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        $this->expectException(ValidationException::class);

        app(ReputationSnapshotService::class)->calculate(Organization::factory()->create(), $business);
    }
}
