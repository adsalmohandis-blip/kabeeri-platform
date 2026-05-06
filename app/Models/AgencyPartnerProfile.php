<?php

namespace App\Models;

use Database\Factories\AgencyPartnerProfileFactory;
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
    'company_id',
    'user_id',
    'display_name',
    'slug',
    'agency_type',
    'status',
    'accreditation_status',
    'accreditation_level',
    'service_categories',
    'regions',
    'languages',
    'submitted_at',
    'accredited_at',
    'metadata',
])]
class AgencyPartnerProfile extends Model
{
    /** @use HasFactory<AgencyPartnerProfileFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $profile): void {
            if (blank($profile->ulid)) {
                $profile->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service_categories' => 'array',
            'regions' => 'array',
            'languages' => 'array',
            'submitted_at' => 'datetime',
            'accredited_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dashboardSnapshots(): HasMany
    {
        return $this->hasMany(AgencyDashboardSnapshot::class);
    }

    public function partnerStorefronts(): HasMany
    {
        return $this->hasMany(PartnerStorefront::class);
    }
}
