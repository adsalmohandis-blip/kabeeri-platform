<?php

namespace App\Models;

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
    'site_id',
    'content_type_id',
    'author_user_id',
    'title',
    'slug',
    'excerpt',
    'body',
    'status',
    'visibility',
    'published_at',
    'seo',
    'seo_title',
    'seo_description',
    'canonical_url',
    'og_title',
    'og_description',
    'og_image_media_id',
    'noindex',
    'metadata',
])]
class ContentEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $entry): void {
            if (blank($entry->ulid)) {
                $entry->ulid = (string) Str::ulid();
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
            'published_at' => 'datetime',
            'seo' => 'array',
            'noindex' => 'boolean',
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

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_media_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ContentRevision::class);
    }

    public function taxonomyTerms(): BelongsToMany
    {
        return $this->belongsToMany(TaxonomyTerm::class, 'content_taxonomy_term')
            ->withTimestamps();
    }
}
