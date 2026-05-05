<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'key',
    'name',
    'slug',
    'package_type',
    'publisher_type',
    'status',
    'short_description',
    'description',
    'category',
    'permissions',
    'dependencies',
    'compatibility',
    'metadata',
])]
class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $package): void {
            if (blank($package->ulid)) {
                $package->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'dependencies' => 'array',
            'compatibility' => 'array',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
