<?php

namespace App\Models;

use Database\Factories\ExternalSourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid', 'organization_id', 'company_id', 'site_id', 'source_type', 'source_name',
    'source_url', 'connection_type', 'status', 'last_sync_at', 'settings', 'metadata',
])]
class ExternalSource extends Model
{
    /** @use HasFactory<ExternalSourceFactory> */
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
            'last_sync_at' => 'datetime',
            'settings' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function csvImports(): HasMany
    {
        return $this->hasMany(CsvImport::class);
    }

    public function mallSyncSources(): HasMany
    {
        return $this->hasMany(MallSyncSource::class);
    }
}
