<?php

namespace App\Models;

use Database\Factories\AcademyBadgeAwardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'academy_badge_id',
    'user_id',
    'work_network_profile_id',
    'awarded_by_user_id',
    'status',
    'awarded_at',
    'expires_at',
    'metadata',
])]
class AcademyBadgeAward extends Model
{
    /** @use HasFactory<AcademyBadgeAwardFactory> */
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

    public function academyBadge(): BelongsTo
    {
        return $this->belongsTo(AcademyBadge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workNetworkProfile(): BelongsTo
    {
        return $this->belongsTo(WorkNetworkProfile::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by_user_id');
    }
}
