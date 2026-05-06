<?php

namespace App\Models;

use Database\Factories\WorkflowRunFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'workflow_definition_id',
    'subject_type',
    'subject_id',
    'status',
    'current_step',
    'context',
    'started_at',
    'completed_at',
    'failed_at',
    'metadata',
])]
class WorkflowRun extends Model
{
    /** @use HasFactory<WorkflowRunFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $run): void {
            if (blank($run->ulid)) {
                $run->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'context' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class, 'workflow_definition_id');
    }
}
