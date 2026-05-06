<?php

namespace App\Models;

use Database\Factories\CloudDomainFactory;
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
    'cloud_site_id',
    'domain',
    'domain_type',
    'verification_status',
    'dns_status',
    'ssl_status',
    'is_primary',
    'verified_at',
    'last_checked_at',
    'dns_records',
    'metadata',
])]
class CloudDomain extends Model
{
    /** @use HasFactory<CloudDomainFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $domain): void {
            if (blank($domain->ulid)) {
                $domain->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'verified_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'dns_records' => 'array',
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

    public function cloudSite(): BelongsTo
    {
        return $this->belongsTo(CloudSite::class);
    }
}
