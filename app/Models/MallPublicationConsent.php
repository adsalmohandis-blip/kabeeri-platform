<?php

namespace App\Models;

use Database\Factories\MallPublicationConsentFactory;
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
    'company_id',
    'site_id',
    'subject_type',
    'subject_id',
    'consent_type',
    'status',
    'channels',
    'granted_by',
    'granted_at',
    'revoked_at',
    'metadata',
])]
class MallPublicationConsent extends Model
{
    /** @use HasFactory<MallPublicationConsentFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $consent): void {
            if (blank($consent->ulid)) {
                $consent->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
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

    public function granter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
