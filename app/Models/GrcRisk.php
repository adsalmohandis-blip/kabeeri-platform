<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['ulid', 'organization_id', 'title', 'status', 'risk_level', 'metadata'])]
class GrcRisk extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
