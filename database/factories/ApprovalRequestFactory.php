<?php

namespace Database\Factories;

use App\Models\ApprovalRequest;
use App\Models\BusinessTask;
use App\Models\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApprovalRequest>
 */
class ApprovalRequestFactory extends Factory
{
    protected $model = ApprovalRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subject = BusinessTask::factory()->create();

        return [
            'organization_id' => $subject->organization_id,
            'workflow_run_id' => null,
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'requested_by_employee_profile_id' => null,
            'approver_employee_profile_id' => EmployeeProfile::factory()->create(['organization_id' => $subject->organization_id])->id,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => 'pending',
            'requested_at' => now(),
            'decided_at' => null,
            'decision_notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
