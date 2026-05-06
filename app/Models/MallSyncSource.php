<?php

namespace App\Models;

use Database\Factories\MallSyncSourceFactory;
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
    'external_source_id',
    'source_name',
    'source_type',
    'sync_scope',
    'sync_direction',
    'status',
    'last_preview_at',
    'last_synced_at',
    'settings',
    'metadata',
])]
class MallSyncSource extends Model
{
    /** @use HasFactory<MallSyncSourceFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $source): void {
            if (blank($source->ulid)) {
                $source->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'last_preview_at' => 'datetime',
            'last_synced_at' => 'datetime',
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

    public function externalSource(): BelongsTo
    {
        return $this->belongsTo(ExternalSource::class);
    }
}
