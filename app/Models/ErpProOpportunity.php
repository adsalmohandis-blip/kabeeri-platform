<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'contact_id',
    'lead_id',
    'sales_pipeline_id',
    'sales_pipeline_stage_id',
    'name',
    'status',
    'priority',
    'expected_value',
    'currency_code',
    'probability',
    'expected_close_at',
    'metadata',
])]
class ErpProOpportunity extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'expected_value' => 'decimal:2',
            'probability' => 'integer',
            'expected_close_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
