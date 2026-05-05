<?php

namespace App\Models;

use Database\Factories\PluginBundleItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['plugin_bundle_id', 'package_id', 'requirement_level', 'sort_order', 'settings'])]
class PluginBundleItem extends Model
{
    /** @use HasFactory<PluginBundleItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function pluginBundle(): BelongsTo
    {
        return $this->belongsTo(PluginBundle::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
