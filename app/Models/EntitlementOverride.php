<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'site_id',
    'key',
    'value_type',
    'limit_value',
    'bool_value',
    'string_value',
    'behavior',
    'reason',
    'starts_at',
    'ends_at',
    'metadata',
])]
class EntitlementOverride extends Model
{
    protected function casts(): array
    {
        return [
            'limit_value' => 'integer',
            'bool_value' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'metadata' => 'array',
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
}
