<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Modules\Core\Services\MediaService;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductImage> */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $media = app(MediaService::class)->createAsset([
            'organization_id' => $product->organization_id,
            'site_id' => $product->site_id,
            'disk' => 'public',
            'path' => 'products/image.jpg',
            'relative_path' => 'products/image.jpg',
            'filename' => 'image.jpg',
            'original_filename' => 'image.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => 1000,
            'visibility' => 'public',
        ]);

        return [
            'product_id' => $product->id,
            'media_asset_id' => $media->id,
            'sort_order' => 0,
            'is_featured' => false,
        ];
    }
}
