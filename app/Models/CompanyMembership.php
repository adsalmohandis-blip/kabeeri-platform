<?php

namespace App\Models;

use Database\Factories\CompanyMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'company_id',
    'user_id',
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
])]
class CompanyMembership extends Model
{
    /** @use HasFactory<CompanyMembershipFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $membership): void {
            if (blank($membership->ulid)) {
                $membership->ulid = (string) Str::ulid();
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
            'start_date' => 'date',
            'end_date' => 'date',
            'public_work_history' => 'boolean',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizationMembership(): BelongsTo
    {
        return $this->belongsTo(OrganizationMembership::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(MembershipRoleAssignment::class, 'membership');
    }

    public function permissionOverrides(): MorphMany
    {
        return $this->morphMany(MembershipPermissionOverride::class, 'membership');
    }
}
