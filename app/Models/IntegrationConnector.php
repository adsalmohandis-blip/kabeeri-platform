<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'key',
    'name',
    'provider',
    'connector_type',
    'status',
    'capabilities',
    'settings',
    'metadata',
])]
class IntegrationConnector extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'capabilities' => 'array',
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
