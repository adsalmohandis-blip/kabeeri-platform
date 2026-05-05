<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'name',
    'slug',
    'version',
    'publisher',
    'status',
    'type',
    'category',
    'industries',
    'app_types',
    'price_type',
    'demo_url',
    'preview_images',
    'performance_score',
    'compatibility',
    'supports_rtl',
    'supports_dark_mode',
    'preview_media_id',
    'manifest',
    'metadata',
])]
class Theme extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $theme): void {
            if (blank($theme->ulid)) {
                $theme->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'supports_rtl' => 'boolean',
            'supports_dark_mode' => 'boolean',
            'industries' => 'array',
            'app_types' => 'array',
            'preview_images' => 'array',
            'compatibility' => 'array',
            'performance_score' => 'integer',
            'manifest' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function previewMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'preview_media_id');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ThemeSetting::class);
    }

    public function appRecipes(): HasMany
    {
        return $this->hasMany(ThemeAppRecipe::class);
    }
}
