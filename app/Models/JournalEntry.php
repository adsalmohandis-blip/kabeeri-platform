<?php

namespace App\Models;

use Database\Factories\JournalEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'entry_number',
    'status',
    'entry_date',
    'source_type',
    'source_id',
    'memo',
    'debit_total',
    'credit_total',
    'posted_at',
    'metadata',
])]
class JournalEntry extends Model
{
    /** @use HasFactory<JournalEntryFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $entry): void {
            if (blank($entry->ulid)) {
                $entry->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'debit_total' => 'decimal:2',
            'credit_total' => 'decimal:2',
            'posted_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }
}
