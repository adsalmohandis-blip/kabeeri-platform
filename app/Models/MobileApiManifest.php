<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'mobile_app_config_id', 'version', 'status', 'endpoints', 'capabilities', 'metadata'])]
class MobileApiManifest extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'endpoints' => 'array',
            'capabilities' => 'array',
            'metadata' => 'array',
        ];
    }
}
