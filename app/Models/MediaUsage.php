<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['media_asset_id', 'usable_type', 'usable_id', 'field_name', 'created_at'])]
class MediaUsage extends Model
{
    public const UPDATED_AT = null;

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    public function usable(): MorphTo
    {
        return $this->morphTo();
    }
}
