<?php

namespace App\Models;

use Database\Factories\MallSyncEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'mall_sync_source_id',
    'event_type',
    'status',
    'total_records',
    'processed_records',
    'failed_records',
    'warnings',
    'errors',
    'metadata',
    'started_at',
    'completed_at',
])]
class MallSyncEvent extends Model
{
    /** @use HasFactory<MallSyncEventFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $event): void {
            if (blank($event->ulid)) {
                $event->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'warnings' => 'array',
            'errors' => 'array',
            'metadata' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function mallSyncSource(): BelongsTo
    {
        return $this->belongsTo(MallSyncSource::class);
    }
}
