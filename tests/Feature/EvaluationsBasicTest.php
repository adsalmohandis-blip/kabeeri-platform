<?php

namespace Tests\Feature;

use App\Models\EmployeeProfile;
use App\Modules\BusinessOperations\Services\EmployeeEvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EvaluationsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_evaluation_can_be_submitted_and_approved(): void
    {
        $employee = EmployeeProfile::factory()->create();
        $reviewer = EmployeeProfile::factory()->create(['organization_id' => $employee->organization_id]);
        $service = app(EmployeeEvaluationService::class);

        $evaluation = $service->createDraft($employee, [
            'reviewer_employee_profile_id' => $reviewer->id,
            'period_label' => 'Q1 2026',
        ]);
        $submitted = $service->submit($evaluation, 88, 'Strong delivery');
        $approved = $service->approve($submitted);

        $this->assertNotNull($approved->ulid);
        $this->assertSame('approved', $approved->status);
        $this->assertSame(88, $approved->score);
        $this->assertNotNull($approved->submitted_at);
        $this->assertNotNull($approved->approved_at);
    }

    public function test_evaluation_score_must_be_between_zero_and_hundred(): void
    {
        $this->expectException(ValidationException::class);

        app(EmployeeEvaluationService::class)->submit(
            app(EmployeeEvaluationService::class)->createDraft(EmployeeProfile::factory()->create(), ['period_label' => 'Q1']),
            101
        );
    }
}
