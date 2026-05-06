<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ulid',
    'organization_id',
    'module_key',
    'meter_key',
    'quantity',
    'unit',
    'usage_date',
    'billable_type',
    'billable_id',
    'metadata',
])]
class BillingUsageRecord extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'usage_date' => 'date',
            'metadata' => 'array',
        ];
    }
}
