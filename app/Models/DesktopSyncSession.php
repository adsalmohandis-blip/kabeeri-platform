<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'desktop_client_id', 'session_uuid', 'direction', 'status', 'cursor', 'started_at', 'last_pull_at', 'last_push_dry_run_at', 'completed_at', 'summary', 'metadata'])]
class DesktopSyncSession extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_pull_at' => 'datetime',
            'last_push_dry_run_at' => 'datetime',
            'completed_at' => 'datetime',
            'summary' => 'array',
            'metadata' => 'array',
        ];
    }
}
