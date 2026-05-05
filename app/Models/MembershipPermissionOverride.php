<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'membership_type',
    'membership_id',
    'permission_id',
    'effect',
    'reason',
    'created_by',
    'created_at',
])]
class MembershipPermissionOverride extends Model
{
    public $timestamps = false;

    public function membership(): MorphTo
    {
        return $this->morphTo();
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
