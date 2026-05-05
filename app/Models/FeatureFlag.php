<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['key', 'description', 'default_value', 'status'])]
class FeatureFlag extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_value' => 'boolean',
        ];
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(FeatureFlagOverride::class);
    }
}
