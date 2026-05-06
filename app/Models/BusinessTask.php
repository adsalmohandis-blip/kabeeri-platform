<?php

namespace App\Models;

use Database\Factories\BusinessTaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'business_project_id',
    'assigned_employee_profile_id',
    'title',
    'description',
    'status',
    'priority',
    'due_on',
    'completed_at',
    'metadata',
])]
class BusinessTask extends Model
{
    /** @use HasFactory<BusinessTaskFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $task): void {
            if (blank($task->ulid)) {
                $task->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'due_on' => 'date',
            'completed_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(BusinessProject::class, 'business_project_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'assigned_employee_profile_id');
    }
}
