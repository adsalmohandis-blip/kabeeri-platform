<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'name',
    'plan_type',
    'status',
    'default_rate',
    'rules',
    'metadata',
])]
class CommissionPlan extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'default_rate' => 'decimal:4',
            'rules' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
