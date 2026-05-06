<?php

namespace App\Models;

use Database\Factories\BusinessProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'organization_id', 'company_id', 'lead_id', 'name', 'slug', 'status', 'starts_on', 'ends_on', 'metadata'])]
class BusinessProject extends Model
{
    /** @use HasFactory<BusinessProjectFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $project): void {
            if (blank($project->ulid)) {
                $project->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(BusinessTask::class);
    }
}
