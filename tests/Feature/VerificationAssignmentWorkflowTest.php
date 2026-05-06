<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VerificationRequest;
use App\Modules\Rabet\Services\VerificationAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VerificationAssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_request_can_be_assigned_to_reviewer(): void
    {
        $request = VerificationRequest::factory()->create(['status' => 'draft']);
        $reviewer = User::factory()->create();

        $assigned = app(VerificationAssignmentService::class)->assign($request, $reviewer, now()->addDays(3));

        $this->assertSame('submitted', $assigned->status);
        $this->assertSame('assigned', $assigned->assignment_status);
        $this->assertSame($reviewer->id, $assigned->assigned_to_user_id);
        $this->assertNotNull($assigned->submitted_at);
        $this->assertNotNull($assigned->assigned_at);
        $this->assertNotNull($assigned->due_at);
    }

    public function test_assigned_reviewer_can_start_and_complete_review(): void
    {
        $request = VerificationRequest::factory()->create(['status' => 'submitted']);
        $reviewer = User::factory()->create();
        $service = app(VerificationAssignmentService::class);

        $assigned = $service->assign($request, $reviewer);
        $inReview = $service->startReview($assigned, $reviewer);

        $this->assertSame('in_review', $inReview->status);

        $completed = $service->complete($inReview, $reviewer, 'approved', 'Documents verified.');

        $this->assertSame('approved', $completed->status);
        $this->assertSame('completed', $completed->assignment_status);
        $this->assertSame($reviewer->id, $completed->reviewed_by);
        $this->assertSame('Documents verified.', $completed->notes);
        $this->assertNotNull($completed->reviewed_at);
    }

    public function test_unassigned_reviewer_cannot_complete_review(): void
    {
        $request = VerificationRequest::factory()->create(['status' => 'submitted']);
        $reviewer = User::factory()->create();
        $otherReviewer = User::factory()->create();

        $assigned = app(VerificationAssignmentService::class)->assign($request, $reviewer);

        $this->expectException(ValidationException::class);

        app(VerificationAssignmentService::class)->complete($assigned, $otherReviewer, 'approved');
    }

    public function test_completed_verification_request_cannot_be_reassigned(): void
    {
        $request = VerificationRequest::factory()->create(['status' => 'approved']);

        $this->expectException(ValidationException::class);

        app(VerificationAssignmentService::class)->assign($request, User::factory()->create());
    }
}
