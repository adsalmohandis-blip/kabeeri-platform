<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Site;
use App\Modules\Core\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceLiteProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_catalog_is_tenant_scoped(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $category = ProductCategory::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'slug' => 'services',
        ]);
        $product = Product::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'category_id' => $category->id,
            'slug' => 'consulting-package',
            'price' => 250,
            'currency_code' => 'USD',
        ]);

        $this->assertTrue($organization->products()->whereKey($product->id)->exists());
        $this->assertTrue($site->products()->whereKey($product->id)->exists());
        $this->assertSame($category->id, $product->category->id);
        $this->assertSame('250.00', $product->price);
    }

    public function test_product_can_have_images(): void
    {
        $product = Product::factory()->create();
        $media = app(MediaService::class)->createAsset([
            'organization_id' => $product->organization_id,
            'site_id' => $product->site_id,
            'disk' => 'public',
            'path' => 'products/featured.jpg',
            'relative_path' => 'products/featured.jpg',
            'filename' => 'featured.jpg',
            'original_filename' => 'featured.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => 2048,
            'visibility' => 'public',
        ]);

        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'media_asset_id' => $media->id,
            'is_featured' => true,
        ]);

        $this->assertTrue($product->images()->whereKey($image->id)->exists());
        $this->assertTrue($image->mediaAsset instanceof MediaAsset);
        $this->assertTrue($image->is_featured);
    }
}
