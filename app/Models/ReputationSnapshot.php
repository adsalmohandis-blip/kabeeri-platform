<?php

namespace App\Models;

use Database\Factories\ReputationSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'subject_type',
    'subject_id',
    'review_count',
    'published_review_count',
    'average_rating',
    'open_moderation_cases_count',
    'trust_score',
    'calculated_at',
    'metadata',
])]
class ReputationSnapshot extends Model
{
    /** @use HasFactory<ReputationSnapshotFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $snapshot): void {
            if (blank($snapshot->ulid)) {
                $snapshot->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'review_count' => 'integer',
            'published_review_count' => 'integer',
            'average_rating' => 'decimal:2',
            'open_moderation_cases_count' => 'integer',
            'trust_score' => 'integer',
            'calculated_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
