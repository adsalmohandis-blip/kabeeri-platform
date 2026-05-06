<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Mall\Services\ModerationCaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ModerationCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderation_case_can_be_opened_for_mall_subject(): void
    {
        $business = MallMirrorBusiness::factory()->create();

        $case = app(ModerationCaseService::class)->open($business->organization, $business, [
            'case_type' => 'listing_quality',
            'reason' => 'incomplete_profile',
            'priority' => 'high',
        ]);

        $this->assertDatabaseHas('moderation_cases', [
            'id' => $case->id,
            'organization_id' => $business->organization_id,
            'site_id' => $business->site_id,
            'subject_type' => $business->getMorphClass(),
            'subject_id' => $business->id,
            'status' => 'open',
            'priority' => 'high',
        ]);
        $this->assertNotNull($case->case_number);
    }

    public function test_moderation_case_rejects_cross_tenant_subject(): void
    {
        $organization = Organization::factory()->create();
        $business = MallMirrorBusiness::factory()->create();

        $this->expectException(ValidationException::class);

        app(ModerationCaseService::class)->open($organization, $business);
    }

    public function test_moderation_case_can_be_assigned_and_resolved(): void
    {
        $case = app(ModerationCaseService::class)->open(
            ($business = MallMirrorBusiness::factory()->create())->organization,
            $business,
        );
        $assignee = User::factory()->create();

        $service = app(ModerationCaseService::class);
        $assigned = $service->assign($case, $assignee);

        $this->assertSame('in_review', $assigned->status);
        $this->assertSame($assignee->id, $assigned->assigned_to_user_id);

        $resolved = $service->resolve($assigned, [
            'decision' => 'approved',
            'notes' => 'Listing is acceptable.',
        ]);

        $this->assertSame('resolved', $resolved->status);
        $this->assertSame('approved', $resolved->resolution['decision']);
        $this->assertNotNull($resolved->resolved_at);
    }
}
