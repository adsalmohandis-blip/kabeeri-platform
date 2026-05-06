<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ulid',
    'organization_id',
    'contact_id',
    'assigned_user_id',
    'ticketable_type',
    'ticketable_id',
    'ticket_number',
    'subject',
    'channel',
    'status',
    'priority',
    'first_response_due_at',
    'resolved_at',
    'metadata',
])]
class HelpdeskTicket extends Model
{
    use HasUlid, SoftDeletes;

    protected function casts(): array
    {
        return [
            'first_response_due_at' => 'datetime',
            'resolved_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
