<?php

namespace App\Models;

use Database\Factories\EmployeeEvaluationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'employee_profile_id',
    'reviewer_employee_profile_id',
    'period_label',
    'period_start',
    'period_end',
    'score',
    'status',
    'summary',
    'criteria',
    'submitted_at',
    'approved_at',
    'metadata',
])]
class EmployeeEvaluation extends Model
{
    /** @use HasFactory<EmployeeEvaluationFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $evaluation): void {
            if (blank($evaluation->ulid)) {
                $evaluation->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'score' => 'integer',
            'criteria' => 'array',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'reviewer_employee_profile_id');
    }
}
