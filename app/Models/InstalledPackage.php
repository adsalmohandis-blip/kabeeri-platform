<?php

namespace App\Models;

use Database\Factories\InstalledPackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'site_id', 'package_id', 'status', 'installed_by', 'installed_at', 'settings'])]
class InstalledPackage extends Model
{
    /** @use HasFactory<InstalledPackageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
