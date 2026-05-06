<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'mobile_device_id', 'provider', 'token_hash', 'status', 'last_used_at', 'metadata'])]
class MobilePushToken extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
