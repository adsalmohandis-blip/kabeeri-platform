<?php

namespace App\Models;

use Database\Factories\TrustBadgeAwardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'trust_badge_id',
    'subject_type',
    'subject_id',
    'awarded_by_user_id',
    'status',
    'awarded_at',
    'expires_at',
    'metadata',
])]
class TrustBadgeAward extends Model
{
    /** @use HasFactory<TrustBadgeAwardFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $award): void {
            if (blank($award->ulid)) {
                $award->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'awarded_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
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

    public function trustBadge(): BelongsTo
    {
        return $this->belongsTo(TrustBadge::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by_user_id');
    }
}
