<?php

namespace App\Models;

use Database\Factories\InventoryItemFactory;
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
    'name',
    'sku',
    'item_type',
    'unit_of_measure',
    'status',
    'cost_price',
    'sale_price',
    'reorder_level',
    'current_quantity',
    'metadata',
])]
class InventoryItem extends Model
{
    /** @use HasFactory<InventoryItemFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $item): void {
            if (blank($item->ulid)) {
                $item->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'current_quantity' => 'decimal:2',
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

    public function needsReorder(): bool
    {
        return $this->reorder_level !== null
            && (float) $this->current_quantity <= (float) $this->reorder_level;
    }
}
