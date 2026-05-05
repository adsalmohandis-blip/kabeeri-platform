<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'key',
    'name',
    'description',
    'module_type',
    'status',
    'version',
    'requires',
])]
class Module extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requires' => 'array',
        ];
    }

    public function installations(): HasMany
    {
        return $this->hasMany(ModuleInstallation::class);
    }
}
