<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'plan_id',
    'key',
    'value_type',
    'limit_value',
    'bool_value',
    'string_value',
    'reset_period',
    'behavior',
    'upgrade_plan_code',
    'metadata',
])]
class PlanEntitlement extends Model
{
    protected function casts(): array
    {
        return [
            'limit_value' => 'integer',
            'bool_value' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
