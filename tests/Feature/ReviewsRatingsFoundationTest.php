<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\Organization;
use App\Models\Review;
use App\Models\User;
use App\Modules\Mall\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReviewsRatingsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_can_be_submitted_as_pending_for_tenant_subject(): void
    {
        $business = MallMirrorBusiness::factory()->create();
        $reviewer = User::factory()->create();

        $review = app(ReviewService::class)->submit($business->organization, $business, $reviewer, [
            'rating' => 5,
            'title' => 'Excellent',
            'body' => 'Helpful and accurate listing.',
        ]);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'reviewable_type' => $business->getMorphClass(),
            'reviewable_id' => $business->id,
            'reviewer_user_id' => $reviewer->id,
            'rating' => 5,
            'status' => 'pending',
        ]);
        $this->assertNotNull($review->submitted_at);
    }

    public function test_review_can_be_approved_or_rejected(): void
    {
        $review = Review::factory()->create(['status' => 'pending']);
        $service = app(ReviewService::class);

        $approved = $service->approve($review);

        $this->assertSame('published', $approved->status);
        $this->assertNotNull($approved->published_at);

        $rejected = $service->reject($approved, 'spam');

        $this->assertSame('rejected', $rejected->status);
        $this->assertNull($rejected->published_at);
        $this->assertSame('spam', $rejected->metadata['rejection_reason']);
    }

    public function test_review_rejects_invalid_rating(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        $this->expectException(ValidationException::class);

        app(ReviewService::class)->submit($business->organization, $business, null, [
            'rating' => 6,
        ]);
    }

    public function test_review_rejects_cross_tenant_target(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        $this->expectException(ValidationException::class);

        app(ReviewService::class)->submit(Organization::factory()->create(), $business, null, [
            'rating' => 4,
        ]);
    }
}
