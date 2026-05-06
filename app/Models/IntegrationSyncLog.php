<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'organization_id',
    'integration_sync_job_id',
    'integration_connector_id',
    'level',
    'message',
    'error_code',
    'context',
])]
class IntegrationSyncLog extends Model
{
    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }
}
