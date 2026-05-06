<?php

namespace Database\Factories;

use App\Models\EmployeeEvaluation;
use App\Models\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeEvaluation>
 */
class EmployeeEvaluationFactory extends Factory
{
    protected $model = EmployeeEvaluation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = EmployeeProfile::factory()->create();

        return [
            'organization_id' => $employee->organization_id,
            'employee_profile_id' => $employee->id,
            'reviewer_employee_profile_id' => null,
            'period_label' => 'Q'.fake()->numberBetween(1, 4).' '.today()->year,
            'period_start' => today()->startOfQuarter(),
            'period_end' => today()->endOfQuarter(),
            'score' => null,
            'status' => 'draft',
            'summary' => null,
            'criteria' => ['communication' => null, 'delivery' => null],
            'submitted_at' => null,
            'approved_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
