<?php

namespace App\Models;

use Database\Factories\ImportBatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'batch_type',
    'status',
    'total_records',
    'processed_records',
    'failed_records',
    'metadata',
])]
class ImportBatch extends Model
{
    /** @use HasFactory<ImportBatchFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'total_records' => 'integer',
            'processed_records' => 'integer',
            'failed_records' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }
}
