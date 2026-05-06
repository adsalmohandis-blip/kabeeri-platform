<?php

namespace App\Models;

use Database\Factories\TrustBadgeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'key',
    'name',
    'badge_type',
    'status',
    'description',
    'criteria',
    'metadata',
])]
class TrustBadge extends Model
{
    /** @use HasFactory<TrustBadgeFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $badge): void {
            if (blank($badge->ulid)) {
                $badge->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function awards(): HasMany
    {
        return $this->hasMany(TrustBadgeAward::class);
    }
}
