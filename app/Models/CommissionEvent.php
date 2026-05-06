<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ulid',
    'organization_id',
    'commission_plan_id',
    'partner_storefront_id',
    'commissionable_type',
    'commissionable_id',
    'event_type',
    'status',
    'base_amount',
    'commission_rate',
    'commission_amount',
    'currency_code',
    'earned_at',
    'metadata',
])]
class CommissionEvent extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'base_amount' => 'decimal:2',
            'commission_rate' => 'decimal:4',
            'commission_amount' => 'decimal:2',
            'earned_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
