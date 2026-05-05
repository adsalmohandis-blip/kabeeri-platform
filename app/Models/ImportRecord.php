<?php

namespace App\Models;

use Database\Factories\ImportRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'source_entity_type',
    'source_entity_id',
    'target_type',
    'target_id',
    'status',
    'warnings',
    'errors',
    'source_payload',
    'metadata',
])]
class ImportRecord extends Model
{
    /** @use HasFactory<ImportRecordFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'warnings' => 'array',
            'errors' => 'array',
            'source_payload' => 'array',
            'metadata' => 'array',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }
}
