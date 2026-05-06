<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'organization_id', 'name', 'source_type', 'status', 'schedule', 'metadata'])]
class DataIngestionPipeline extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
            'metadata' => 'array',
        ];
    }
}
