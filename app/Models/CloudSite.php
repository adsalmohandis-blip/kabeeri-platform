<?php

namespace App\Models;

use Database\Factories\CloudSiteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'environment',
    'provider',
    'region',
    'deployment_status',
    'health_status',
    'public_url',
    'last_deployed_at',
    'last_checked_at',
    'settings',
    'metadata',
])]
class CloudSite extends Model
{
    /** @use HasFactory<CloudSiteFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $cloudSite): void {
            if (blank($cloudSite->ulid)) {
                $cloudSite->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'last_deployed_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
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
}
