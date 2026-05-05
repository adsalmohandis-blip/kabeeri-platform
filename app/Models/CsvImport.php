<?php

namespace App\Models;

use Database\Factories\CsvImportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid', 'external_source_id', 'file_path', 'status', 'total_rows', 'imported_count',
    'skipped_count', 'error_count', 'errors', 'metadata', 'started_at', 'completed_at',
])]
class CsvImport extends Model
{
    /** @use HasFactory<CsvImportFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $import): void {
            if (blank($import->ulid)) {
                $import->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'errors' => 'array',
            'metadata' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function externalSource(): BelongsTo
    {
        return $this->belongsTo(ExternalSource::class);
    }
}
