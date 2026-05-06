<?php

namespace App\Models;

use Database\Factories\TravelTourismMallListingFactory;
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
    'site_id',
    'mall_publication_consent_id',
    'listing_type',
    'title',
    'slug',
    'description',
    'destination',
    'country_code',
    'price_from',
    'currency',
    'availability',
    'contact_channels',
    'images',
    'listing_status',
    'published_at',
    'last_refreshed_at',
    'metadata',
])]
class TravelTourismMallListing extends Model
{
    /** @use HasFactory<TravelTourismMallListingFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $listing): void {
            if (blank($listing->ulid)) {
                $listing->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price_from' => 'decimal:2',
            'availability' => 'array',
            'contact_channels' => 'array',
            'images' => 'array',
            'published_at' => 'datetime',
            'last_refreshed_at' => 'datetime',
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

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function publicationConsent(): BelongsTo
    {
        return $this->belongsTo(MallPublicationConsent::class, 'mall_publication_consent_id');
    }
}
