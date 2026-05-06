<?php

namespace Tests\Feature;

use App\Models\ApprovalRequest;
use App\Models\BusinessTask;
use App\Models\EmployeeProfile;
use App\Modules\BusinessOperations\Services\ApprovalRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApprovalRequestsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_request_can_be_approved(): void
    {
        $task = BusinessTask::factory()->create();
        $approver = EmployeeProfile::factory()->create(['organization_id' => $task->organization_id]);

        $request = app(ApprovalRequestService::class)->request($task, $approver, ['title' => 'Approve task']);
        $approved = app(ApprovalRequestService::class)->approve($request, 'Looks good');

        $this->assertNotNull($approved->ulid);
        $this->assertSame('approved', $approved->status);
        $this->assertSame('Looks good', $approved->decision_notes);
        $this->assertNotNull($approved->decided_at);
    }

    public function test_approval_request_can_be_rejected(): void
    {
        $request = ApprovalRequest::factory()->create();

        $rejected = app(ApprovalRequestService::class)->reject($request, 'Needs edits');

        $this->assertSame('rejected', $rejected->status);
    }

    public function test_decided_request_cannot_be_decided_again(): void
    {
        $request = app(ApprovalRequestService::class)->approve(ApprovalRequest::factory()->create());

        $this->expectException(ValidationException::class);

        app(ApprovalRequestService::class)->reject($request);
    }
}
