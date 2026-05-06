<?php

namespace App\Models;

use Database\Factories\PartnerCatalogShareFactory;
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
    'partner_storefront_id',
    'catalogable_type',
    'catalogable_id',
    'share_type',
    'status',
    'visibility',
    'sort_order',
    'metadata',
])]
class PartnerCatalogShare extends Model
{
    /** @use HasFactory<PartnerCatalogShareFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $share): void {
            if (blank($share->ulid)) {
                $share->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function partnerStorefront(): BelongsTo
    {
        return $this->belongsTo(PartnerStorefront::class);
    }

    public function catalogable(): MorphTo
    {
        return $this->morphTo();
    }
}
