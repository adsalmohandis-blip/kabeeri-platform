<?php

namespace App\Models;

use Database\Factories\ReportSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'organization_id', 'report_definition_id', 'status', 'parameters', 'data', 'generated_at', 'metadata'])]
class ReportSnapshot extends Model
{
    /** @use HasFactory<ReportSnapshotFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $snapshot): void {
            if (blank($snapshot->ulid)) {
                $snapshot->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'data' => 'array',
            'generated_at' => 'datetime',
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
        return $this->belongsTo(ReportDefinition::class, 'report_definition_id');
    }
}
