<?php

namespace App\Models;

use Database\Factories\MallMirrorCourseFactory;
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
    'course_id',
    'mall_publication_consent_id',
    'course_name',
    'slug',
    'description',
    'training_type',
    'delivery_mode',
    'price',
    'currency',
    'schedule',
    'instructor_info',
    'images',
    'mirror_status',
    'published_at',
    'last_refreshed_at',
    'metadata',
])]
class MallMirrorCourse extends Model
{
    /** @use HasFactory<MallMirrorCourseFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $course): void {
            if (blank($course->ulid)) {
                $course->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'schedule' => 'array',
            'instructor_info' => 'array',
            'images' => 'array',
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

    public function publicationConsent(): BelongsTo
    {
        return $this->belongsTo(MallPublicationConsent::class, 'mall_publication_consent_id');
    }
}
