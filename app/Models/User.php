<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (blank($user->ulid)) {
                $user->ulid = (string) Str::ulid();
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_user_id');
    }

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_memberships')
            ->withPivot([
                'membership_type',
                'status',
                'job_title',
                'invited_by',
                'invited_at',
                'accepted_at',
                'start_date',
                'end_date',
                'visibility',
                'metadata',
                'deleted_at',
            ])
            ->withTimestamps();
    }

    public function companyMemberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_memberships')
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

    public function createdSites(): HasMany
    {
        return $this->hasMany(Site::class, 'created_by');
    }

    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_user_id');
    }

    public function uploadedMediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class, 'uploaded_by');
    }

    public function moduleInstallations(): HasMany
    {
        return $this->hasMany(ModuleInstallation::class, 'installed_by');
    }

    public function authoredContentEntries(): HasMany
    {
        return $this->hasMany(ContentEntry::class, 'author_user_id');
    }

    public function contentRevisions(): HasMany
    {
        return $this->hasMany(ContentRevision::class, 'created_by');
    }

    public function requestedVerificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class, 'requested_by');
    }

    public function reviewedVerificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class, 'reviewed_by');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->exists;
    }
}
