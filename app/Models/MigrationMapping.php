<?php

namespace App\Models;

use Database\Factories\MigrationMappingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'source_type',
    'source_id',
    'source_key',
    'target_type',
    'target_id',
    'mapping_status',
    'mapping_strategy',
    'metadata',
])]
class MigrationMapping extends Model
{
    /** @use HasFactory<MigrationMappingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }
}
