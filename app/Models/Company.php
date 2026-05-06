<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'legal_name',
    'trade_name',
    'slug',
    'country_id',
    'city',
    'legal_type',
    'registration_number',
    'tax_number',
    'industry',
    'company_size',
    'status',
    'verification_status',
    'verified_at',
    'verification_expires_at',
    'metadata',
])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $company): void {
            if (blank($company->ulid)) {
                $company->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'verification_expires_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function mediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function moduleInstallations(): HasMany
    {
        return $this->hasMany(ModuleInstallation::class);
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }

    public function backofficeWorkspaces(): HasMany
    {
        return $this->hasMany(BackofficeWorkspace::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function legalPartnerProfiles(): HasMany
    {
        return $this->hasMany(LegalPartnerProfile::class);
    }

    public function creatorProfiles(): HasMany
    {
        return $this->hasMany(CreatorProfile::class);
    }

    public function agencyPartnerProfiles(): HasMany
    {
        return $this->hasMany(AgencyPartnerProfile::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_memberships')
            ->withPivot([
                'organization_membership_id',
                'role_title',
                'department_id',
                'manager_user_id',
                'employment_type',
                'status',
                'start_date',
                'end_date',
                'public_work_history',
                'metadata',
                'deleted_at',
            ])
            ->withTimestamps();
    }
}
