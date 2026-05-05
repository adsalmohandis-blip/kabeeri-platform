<?php

namespace App\Models;

use Database\Factories\PluginBundleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'name', 'slug', 'bundle_type', 'description', 'status', 'metadata'])]
class PluginBundle extends Model
{
    /** @use HasFactory<PluginBundleFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $bundle): void {
            if (blank($bundle->ulid)) {
                $bundle->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PluginBundleItem::class)->orderBy('sort_order');
    }
}
