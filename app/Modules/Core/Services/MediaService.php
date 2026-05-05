<?php

namespace App\Modules\Core\Services;

use App\Models\MediaAsset;
use App\Models\MediaUsage;
use Illuminate\Database\Eloquent\Model;

class MediaService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createAsset(array $attributes): MediaAsset
    {
        return MediaAsset::query()->create($attributes);
    }

    public function attachUsage(MediaAsset $asset, Model $usable, ?string $fieldName = null): MediaUsage
    {
        return MediaUsage::query()->create([
            'media_asset_id' => $asset->id,
            'usable_type' => $usable->getMorphClass(),
            'usable_id' => $usable->getKey(),
            'field_name' => $fieldName,
        ]);
    }
}
