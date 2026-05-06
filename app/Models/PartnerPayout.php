<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ulid',
    'organization_id',
    'partner_storefront_id',
    'payout_number',
    'status',
    'amount',
    'currency_code',
    'payout_method_reference',
    'approved_at',
    'paid_at',
    'risk_checks',
    'metadata',
])]
class PartnerPayout extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'risk_checks' => 'array',
            'metadata' => 'array',
        ];
    }
}
