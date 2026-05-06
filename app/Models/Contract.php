<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'company_id',
    'contact_id',
    'contractable_type',
    'contractable_id',
    'contract_number',
    'title',
    'status',
    'starts_on',
    'ends_on',
    'terms',
    'metadata',
])]
class Contract extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'terms' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
