<?php

namespace App\Models;

use Database\Factories\WorkflowDefinitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'organization_id', 'name', 'slug', 'trigger_type', 'status', 'steps', 'conditions', 'metadata'])]
class WorkflowDefinition extends Model
{
    /** @use HasFactory<WorkflowDefinitionFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $definition): void {
            if (blank($definition->ulid)) {
                $definition->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'steps' => 'array',
            'conditions' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }
}
