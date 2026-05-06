<?php

namespace App\Models;

use Database\Factories\GrowthReferralFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'referrer_user_id',
    'referred_organization_id',
    'code',
    'referred_email',
    'source',
    'status',
    'accepted_at',
    'expired_at',
    'metadata',
])]
class GrowthReferral extends Model
{
    /** @use HasFactory<GrowthReferralFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $referral): void {
            if (blank($referral->ulid)) {
                $referral->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expired_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referredOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'referred_organization_id');
    }
}
