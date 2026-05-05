<?php

namespace App\Models;

use Database\Factories\SalesPipelineStageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'sales_pipeline_id',
    'name',
    'slug',
    'sort_order',
    'probability',
    'is_won',
    'is_lost',
    'status',
    'metadata',
])]
class SalesPipelineStage extends Model
{
    /** @use HasFactory<SalesPipelineStageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'probability' => 'integer',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(SalesPipeline::class, 'sales_pipeline_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
