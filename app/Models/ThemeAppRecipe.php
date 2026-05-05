<?php

namespace App\Models;

use Database\Factories\ThemeAppRecipeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'theme_id',
    'project_type',
    'app_type',
    'required_packages',
    'recommended_packages',
    'optional_packages',
    'demo_content_ref',
    'setup_steps',
    'metadata',
])]
class ThemeAppRecipe extends Model
{
    /** @use HasFactory<ThemeAppRecipeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'required_packages' => 'array',
            'recommended_packages' => 'array',
            'optional_packages' => 'array',
            'setup_steps' => 'array',
            'metadata' => 'array',
        ];
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
