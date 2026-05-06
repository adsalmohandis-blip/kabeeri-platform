<?php

namespace App\Modules\Rabet\Services;

use App\Models\User;
use App\Models\VerificationRequest;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class VerificationAssignmentService
{
    public function assign(VerificationRequest $request, User $assignee, ?DateTimeInterface $dueAt = null): VerificationRequest
    {
        if (in_array($request->status, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Completed verification requests cannot be reassigned.',
            ]);
        }

        $request->forceFill([
            'assigned_to_user_id' => $assignee->id,
            'assignment_status' => 'assigned',
            'assigned_at' => now(),
            'due_at' => $dueAt,
            'status' => $request->status === 'draft' ? 'submitted' : $request->status,
            'submitted_at' => $request->submitted_at ?? now(),
        ])->save();

        return $request->refresh();
    }

    public function startReview(VerificationRequest $request, User $reviewer): VerificationRequest
    {
        $this->assertAssignedReviewer($request, $reviewer);

        $request->forceFill([
            'assignment_status' => 'in_review',
            'status' => 'in_review',
        ])->save();

        return $request->refresh();
    }

    public function complete(VerificationRequest $request, User $reviewer, string $decision, ?string $notes = null): VerificationRequest
    {
        $this->assertAssignedReviewer($request, $reviewer);

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Verification decision must be approved or rejected.',
            ]);
        }

        $request->forceFill([
            'status' => $decision,
            'assignment_status' => 'completed',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'notes' => $notes ?? $request->notes,
        ])->save();

        return $request->refresh();
    }

    private function assertAssignedReviewer(VerificationRequest $request, User $reviewer): void
    {
        if ((int) $request->assigned_to_user_id !== (int) $reviewer->id) {
            throw ValidationException::withMessages([
                'assigned_to_user_id' => 'Only the assigned reviewer can perform this verification action.',
            ]);
        }
    }
}
