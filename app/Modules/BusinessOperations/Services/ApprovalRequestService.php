<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\ApprovalRequest;
use App\Models\EmployeeProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ApprovalRequestService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function request(Model $subject, EmployeeProfile $approver, array $attributes = []): ApprovalRequest
    {
        $organizationId = $subject->organization_id ?? null;

        if ($organizationId === null || (int) $organizationId !== (int) $approver->organization_id) {
            throw ValidationException::withMessages([
                'approver_employee_profile_id' => 'Approver must belong to the subject organization.',
            ]);
        }

        return ApprovalRequest::query()->create([
            ...$attributes,
            'organization_id' => $organizationId,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'approver_employee_profile_id' => $approver->id,
            'title' => $attributes['title'] ?? 'Approval request',
            'status' => 'pending',
            'requested_at' => now(),
        ]);
    }

    public function approve(ApprovalRequest $request, ?string $notes = null): ApprovalRequest
    {
        return $this->decide($request, 'approved', $notes);
    }

    public function reject(ApprovalRequest $request, ?string $notes = null): ApprovalRequest
    {
        return $this->decide($request, 'rejected', $notes);
    }

    protected function decide(ApprovalRequest $request, string $status, ?string $notes): ApprovalRequest
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending approval requests can be decided.',
            ]);
        }

        $request->forceFill([
            'status' => $status,
            'decision_notes' => $notes,
            'decided_at' => now(),
        ])->save();

        return $request->refresh();
    }
}
