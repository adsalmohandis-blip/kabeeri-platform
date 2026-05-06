<?php

namespace App\Models;

use Database\Factories\CloudHealthCheckFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'cloud_site_id',
    'check_type',
    'status',
    'response_time_ms',
    'status_code',
    'checked_at',
    'message',
    'metadata',
])]
class CloudHealthCheck extends Model
{
    /** @use HasFactory<CloudHealthCheckFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $check): void {
            if (blank($check->ulid)) {
                $check->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'response_time_ms' => 'integer',
            'status_code' => 'integer',
            'checked_at' => 'datetime',
            'metadata' => 'array',
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
