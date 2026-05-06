<?php

namespace App\Models;

use Database\Factories\PartnerStorefrontFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'agency_partner_profile_id',
    'name',
    'slug',
    'storefront_type',
    'status',
    'visibility',
    'settings',
    'metadata',
])]
class PartnerStorefront extends Model
{
    /** @use HasFactory<PartnerStorefrontFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $storefront): void {
            if (blank($storefront->ulid)) {
                $storefront->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function agencyPartnerProfile(): BelongsTo
    {
        return $this->belongsTo(AgencyPartnerProfile::class);
    }

    public function catalogShares(): HasMany
    {
        return $this->hasMany(PartnerCatalogShare::class);
    }
}
