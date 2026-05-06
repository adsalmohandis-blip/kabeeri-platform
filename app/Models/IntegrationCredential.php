<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'integration_connector_id',
    'credential_type',
    'vault_reference',
    'status',
    'last_verified_at',
    'metadata',
])]
class IntegrationCredential extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'last_verified_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
