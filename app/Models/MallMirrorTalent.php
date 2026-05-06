<?php

namespace App\Models;

use Database\Factories\MallMirrorTalentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'company_id',
    'site_id',
    'employee_profile_id',
    'mall_publication_consent_id',
    'display_name',
    'slug',
    'headline',
    'bio',
    'skills',
    'availability',
    'location_label',
    'mirror_status',
    'published_at',
    'last_refreshed_at',
    'metadata',
])]
class MallMirrorTalent extends Model
{
    /** @use HasFactory<MallMirrorTalentFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'mall_mirror_talent';

    protected static function booted(): void
    {
        static::creating(function (self $talent): void {
            if (blank($talent->ulid)) {
                $talent->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'availability' => 'array',
            'published_at' => 'datetime',
            'last_refreshed_at' => 'datetime',
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

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function publicationConsent(): BelongsTo
    {
        return $this->belongsTo(MallPublicationConsent::class, 'mall_publication_consent_id');
    }
}
