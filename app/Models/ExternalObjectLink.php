<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ulid',
    'organization_id',
    'integration_connector_id',
    'linkable_type',
    'linkable_id',
    'external_object_type',
    'external_object_id',
    'sync_direction',
    'status',
    'last_seen_at',
    'metadata',
])]
class ExternalObjectLink extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
