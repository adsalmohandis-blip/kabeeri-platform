<?php

namespace Tests\Feature;

use App\Models\ModerationCase;
use App\Models\User;
use App\Modules\Mall\Services\ModerationQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationQueueAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_lists_only_open_cases_for_organization_by_priority(): void
    {
        $normal = ModerationCase::factory()->create([
            'priority' => 'normal',
            'status' => 'open',
            'opened_at' => now()->subMinutes(10),
        ]);
        $high = ModerationCase::factory()->create([
            'organization_id' => $normal->organization_id,
            'site_id' => $normal->site_id,
            'priority' => 'high',
            'status' => 'open',
            'opened_at' => now(),
        ]);
        ModerationCase::factory()->create([
            'organization_id' => $normal->organization_id,
            'status' => 'resolved',
        ]);
        ModerationCase::factory()->create(['status' => 'open']);

        $cases = app(ModerationQueueService::class)
            ->queryForOrganization($normal->organization)
            ->pluck('id')
            ->all();

        $this->assertSame([$high->id, $normal->id], $cases);
    }

    public function test_queue_can_assign_next_case_to_reviewer(): void
    {
        $case = ModerationCase::factory()->create([
            'priority' => 'urgent',
            'status' => 'open',
        ]);
        $assignee = User::factory()->create();

        $assigned = app(ModerationQueueService::class)->takeNext($case->organization, $assignee);

        $this->assertNotNull($assigned);
        $this->assertSame($case->id, $assigned->id);
        $this->assertSame('in_review', $assigned->status);
        $this->assertSame($assignee->id, $assigned->assigned_to_user_id);
    }

    public function test_queue_status_summary_is_tenant_scoped(): void
    {
        $case = ModerationCase::factory()->create(['status' => 'open']);
        ModerationCase::factory()->create([
            'organization_id' => $case->organization_id,
            'status' => 'resolved',
        ]);
        ModerationCase::factory()->create(['status' => 'open']);

        $summary = app(ModerationQueueService::class)->statusSummary($case->organization);

        $this->assertSame(1, $summary['open']);
        $this->assertSame(1, $summary['resolved']);
    }
}
