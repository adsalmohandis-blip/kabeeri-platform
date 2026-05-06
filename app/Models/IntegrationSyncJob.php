<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ulid',
    'organization_id',
    'integration_connector_id',
    'job_type',
    'status',
    'direction',
    'attempts',
    'scheduled_at',
    'started_at',
    'finished_at',
    'payload',
    'metadata',
])]
class IntegrationSyncJob extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'payload' => 'array',
            'metadata' => 'array',
        ];
    }
}
