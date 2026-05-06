<?php

namespace App\Models;

use Database\Factories\ApprovalRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'workflow_run_id',
    'subject_type',
    'subject_id',
    'requested_by_employee_profile_id',
    'approver_employee_profile_id',
    'title',
    'description',
    'status',
    'requested_at',
    'decided_at',
    'decision_notes',
    'metadata',
])]
class ApprovalRequest extends Model
{
    /** @use HasFactory<ApprovalRequestFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            if (blank($request->ulid)) {
                $request->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'decided_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'requested_by_employee_profile_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'approver_employee_profile_id');
    }
}
