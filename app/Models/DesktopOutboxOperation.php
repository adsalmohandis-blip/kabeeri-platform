<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'desktop_sync_session_id', 'client_operation_id', 'operation_type', 'entity_type', 'entity_id', 'base_version', 'server_version', 'status', 'payload_preview', 'validation_errors', 'received_at'])]
class DesktopOutboxOperation extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'base_version' => 'integer',
            'server_version' => 'integer',
            'payload_preview' => 'array',
            'validation_errors' => 'array',
            'received_at' => 'datetime',
        ];
    }
}
