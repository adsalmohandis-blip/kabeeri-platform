<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\ModerationFlag;
use App\Models\Organization;
use App\Modules\Mall\Services\ModerationFlagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ModerationFlagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_flag_report_creates_moderation_case_by_default(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        $flag = app(ModerationFlagService::class)->report($business->organization, $business, [
            'flag_type' => 'listing_report',
            'reason' => 'misleading_content',
            'severity' => 'high',
            'message' => 'This listing needs review.',
            'evidence' => [['type' => 'url', 'value' => 'https://example.test/listing']],
        ]);

        $this->assertDatabaseHas('moderation_flags', [
            'id' => $flag->id,
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'moderation_case_id' => $flag->moderation_case_id,
            'subject_type' => $business->getMorphClass(),
            'subject_id' => $business->id,
            'status' => 'new',
            'severity' => 'high',
        ]);
        $this->assertNotNull($flag->moderationCase);
        $this->assertSame('listing_report', $flag->moderationCase->case_type);
    }

    public function test_flag_can_be_reviewed_and_dismissed(): void
    {
        $flag = ModerationFlag::factory()->create();
        $service = app(ModerationFlagService::class);

        $reviewed = $service->markReviewed($flag);

        $this->assertSame('reviewed', $reviewed->status);
        $this->assertNotNull($reviewed->reviewed_at);

        $dismissed = $service->dismiss($reviewed, 'duplicate report');

        $this->assertSame('dismissed', $dismissed->status);
        $this->assertSame('duplicate report', $dismissed->metadata['dismissed_reason']);
    }

    public function test_flag_rejects_cross_tenant_subject(): void
    {
        $organization = Organization::factory()->create();
        $business = MallMirrorBusiness::factory()->create();

        $this->expectException(ValidationException::class);

        app(ModerationFlagService::class)->report($organization, $business);
    }
}
