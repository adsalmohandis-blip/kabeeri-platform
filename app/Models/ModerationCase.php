<?php

namespace App\Models;

use Database\Factories\ModerationCaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'site_id',
    'subject_type',
    'subject_id',
    'case_number',
    'case_type',
    'reason',
    'priority',
    'status',
    'reported_by_user_id',
    'assigned_to_user_id',
    'opened_at',
    'resolved_at',
    'closed_at',
    'resolution',
    'metadata',
])]
class ModerationCase extends Model
{
    /** @use HasFactory<ModerationCaseFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $case): void {
            if (blank($case->ulid)) {
                $case->ulid = (string) Str::ulid();
            }

            if (blank($case->case_number)) {
                $case->case_number = 'MOD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'resolution' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
