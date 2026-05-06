<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid', 'organization_id', 'site_id', 'company_id', 'category_id', 'name', 'slug',
    'description', 'short_description', 'status', 'visibility', 'price', 'currency_code',
    'sku', 'stock_status', 'seo', 'metadata',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
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
            'seo' => 'array',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
