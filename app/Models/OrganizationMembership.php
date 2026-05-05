<?php

namespace App\Models;

use Database\Factories\OrganizationMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'user_id',
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
])]
class OrganizationMembership extends Model
{
    /** @use HasFactory<OrganizationMembershipFactory> */
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
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
            'start_date' => 'date',
            'end_date' => 'date',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function companyMemberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
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
