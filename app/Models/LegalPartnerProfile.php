<?php

namespace App\Models;

use Database\Factories\LegalPartnerProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'company_id',
    'user_id',
    'display_name',
    'slug',
    'partner_type',
    'practice_areas',
    'jurisdictions',
    'languages',
    'contact_channels',
    'verification_status',
    'network_status',
    'verified_at',
    'activated_at',
    'metadata',
])]
class LegalPartnerProfile extends Model
{
    /** @use HasFactory<LegalPartnerProfileFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $profile): void {
            if (blank($profile->ulid)) {
                $profile->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'practice_areas' => 'array',
            'jurisdictions' => 'array',
            'languages' => 'array',
            'contact_channels' => 'array',
            'verified_at' => 'datetime',
            'activated_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
