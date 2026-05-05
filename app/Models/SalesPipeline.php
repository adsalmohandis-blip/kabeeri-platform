<?php

namespace App\Models;

use Database\Factories\SalesPipelineFactory;
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
    'name',
    'slug',
    'status',
    'is_default',
    'metadata',
])]
class SalesPipeline extends Model
{
    /** @use HasFactory<SalesPipelineFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $pipeline): void {
            if (blank($pipeline->ulid)) {
                $pipeline->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(SalesPipelineStage::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
