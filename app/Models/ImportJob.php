<?php

namespace App\Models;

use Database\Factories\ImportJobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'source_type',
    'source_name',
    'status',
    'file_media_id',
    'started_by',
    'started_at',
    'completed_at',
    'failed_at',
    'summary',
    'settings',
    'metadata',
])]
class ImportJob extends Model
{
    /** @use HasFactory<ImportJobFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $job): void {
            if (blank($job->ulid)) {
                $job->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'summary' => 'array',
            'settings' => 'array',
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

    public function fileMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'file_media_id');
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ImportBatch::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(ImportRecord::class);
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(MigrationMapping::class);
    }

    public function redirectSuggestions(): HasMany
    {
        return $this->hasMany(RedirectSuggestion::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MigrationReport::class);
    }
}
