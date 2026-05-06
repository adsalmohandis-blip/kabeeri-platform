<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'desktop_client_id', 'desktop_sync_session_id', 'queue_uuid', 'direction', 'filename', 'mime_type', 'size_bytes', 'sha256', 'status', 'storage_reference', 'metadata'])]
class DesktopFileQueueItem extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'metadata' => 'array',
        ];
    }
}
