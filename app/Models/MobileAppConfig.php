<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['ulid', 'organization_id', 'site_id', 'app_key', 'name', 'platform', 'status', 'min_supported_version', 'current_version', 'settings', 'metadata'])]
class MobileAppConfig extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
