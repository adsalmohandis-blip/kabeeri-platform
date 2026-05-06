<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['ulid', 'user_id', 'organization_id', 'client_uuid', 'name', 'platform', 'app_version', 'status', 'last_seen_at', 'capabilities', 'metadata'])]
class DesktopClient extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'capabilities' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
