<?php

namespace App\Models;

use Database\Factories\MallMirrorBusinessFactory;
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
    'business_profile_id',
    'mall_publication_consent_id',
    'display_name',
    'slug',
    'description',
    'public_contacts',
    'mirror_status',
    'published_at',
    'last_refreshed_at',
    'metadata',
])]
class MallMirrorBusiness extends Model
{
    /** @use HasFactory<MallMirrorBusinessFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $business): void {
            if (blank($business->ulid)) {
                $business->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'public_contacts' => 'array',
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

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function publicationConsent(): BelongsTo
    {
        return $this->belongsTo(MallPublicationConsent::class, 'mall_publication_consent_id');
    }
}
