<?php

namespace App\Models;

use Database\Factories\CloudBackupFactory;
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
    'backup_type',
    'status',
    'storage_label',
    'backup_reference',
    'size_bytes',
    'started_at',
    'completed_at',
    'failed_at',
    'errors',
    'metadata',
])]
class CloudBackup extends Model
{
    /** @use HasFactory<CloudBackupFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $backup): void {
            if (blank($backup->ulid)) {
                $backup->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'errors' => 'array',
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
