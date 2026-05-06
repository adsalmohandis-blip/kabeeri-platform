<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'tier',
    'price_cents',
    'currency_code',
    'billing_interval',
    'is_public',
    'is_active',
    'sort_order',
    'metadata',
])]
class Plan extends Model
{
    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(PlanEntitlement::class);
    }
}
