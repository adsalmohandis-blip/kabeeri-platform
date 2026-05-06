<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ulid', 'developer_publisher_id', 'listable_type', 'listable_id', 'listing_type', 'title', 'slug', 'status', 'visibility', 'review_summary', 'metadata'])]
class DeveloperMarketplaceListing extends Model
{
    use HasUlid;

    protected function casts(): array
    {
        return [
            'review_summary' => 'array',
            'metadata' => 'array',
        ];
    }
}
