<?php

namespace App\Models;

use Database\Factories\MigrationReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'organization_id',
    'site_id',
    'status',
    'data',
])]
class MigrationReport extends Model
{
    /** @use HasFactory<MigrationReportFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }
}
