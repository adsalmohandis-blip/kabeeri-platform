<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\EmployeeEvaluation;
use App\Models\EmployeeProfile;
use Illuminate\Validation\ValidationException;

class EmployeeEvaluationService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(EmployeeProfile $employee, array $attributes): EmployeeEvaluation
    {
        if (($attributes['reviewer_employee_profile_id'] ?? null) !== null) {
            $reviewer = EmployeeProfile::query()->findOrFail($attributes['reviewer_employee_profile_id']);

            if ((int) $reviewer->organization_id !== (int) $employee->organization_id) {
                throw ValidationException::withMessages([
                    'reviewer_employee_profile_id' => 'Reviewer must belong to the same organization.',
                ]);
            }
        }

        return EmployeeEvaluation::query()->create([
            ...$attributes,
            'organization_id' => $employee->organization_id,
            'employee_profile_id' => $employee->id,
            'status' => 'draft',
        ]);
    }

    public function submit(EmployeeEvaluation $evaluation, int $score, ?string $summary = null): EmployeeEvaluation
    {
        if ($score < 0 || $score > 100) {
            throw ValidationException::withMessages([
                'score' => 'Evaluation score must be between 0 and 100.',
            ]);
        }

        $evaluation->forceFill([
            'score' => $score,
            'summary' => $summary,
            'status' => 'submitted',
            'submitted_at' => now(),
        ])->save();

        return $evaluation->refresh();
    }

    public function approve(EmployeeEvaluation $evaluation): EmployeeEvaluation
    {
        if ($evaluation->status !== 'submitted') {
            throw ValidationException::withMessages([
                'status' => 'Only submitted evaluations can be approved.',
            ]);
        }

        $evaluation->forceFill([
            'status' => 'approved',
            'approved_at' => now(),
        ])->save();

        return $evaluation->refresh();
    }
}
