<?php

namespace App\Models;

use Database\Factories\AgencyDashboardSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'agency_partner_profile_id',
    'period_start',
    'period_end',
    'metrics',
    'alerts',
    'generated_at',
    'metadata',
])]
class AgencyDashboardSnapshot extends Model
{
    /** @use HasFactory<AgencyDashboardSnapshotFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $snapshot): void {
            if (blank($snapshot->ulid)) {
                $snapshot->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'metrics' => 'array',
            'alerts' => 'array',
            'generated_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function agencyPartnerProfile(): BelongsTo
    {
        return $this->belongsTo(AgencyPartnerProfile::class);
    }
}
