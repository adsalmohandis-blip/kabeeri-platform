<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'user_id', 'mobile_device_id', 'token_hash', 'status', 'last_used_at', 'expires_at', 'abilities', 'metadata'])]
class MobileAuthToken extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'abilities' => 'array',
            'metadata' => 'array',
        ];
    }
}
