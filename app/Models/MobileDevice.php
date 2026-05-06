<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['ulid', 'user_id', 'mobile_app_config_id', 'device_uuid', 'platform', 'app_version', 'status', 'last_seen_at', 'metadata'])]
class MobileDevice extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
