<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'desktop_sync_session_id', 'desktop_outbox_operation_id', 'entity_type', 'entity_id', 'conflict_type', 'status', 'client_snapshot', 'server_snapshot', 'resolution'])]
class DesktopSyncConflict extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'client_snapshot' => 'array',
            'server_snapshot' => 'array',
            'resolution' => 'array',
        ];
    }
}
