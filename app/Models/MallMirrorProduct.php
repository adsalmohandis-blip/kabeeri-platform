<?php

namespace App\Models;

use Database\Factories\MallMirrorProductFactory;
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
    'product_id',
    'mall_publication_consent_id',
    'product_name',
    'slug',
    'description',
    'price',
    'currency',
    'images',
    'attributes',
    'mirror_status',
    'published_at',
    'last_refreshed_at',
    'metadata',
])]
class MallMirrorProduct extends Model
{
    /** @use HasFactory<MallMirrorProductFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            if (blank($product->ulid)) {
                $product->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'images' => 'array',
            'attributes' => 'array',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function publicationConsent(): BelongsTo
    {
        return $this->belongsTo(MallPublicationConsent::class, 'mall_publication_consent_id');
    }
}
