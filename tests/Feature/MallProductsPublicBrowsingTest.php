<?php

namespace Tests\Feature;

use App\Models\MallMirrorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallProductsPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_lists_published_products_only(): void
    {
        MallMirrorProduct::factory()->create([
            'product_name' => 'Published Product',
            'slug' => 'published-product',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Draft Product',
            'slug' => 'draft-product',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall/products')
            ->assertOk()
            ->assertSee('Published Product')
            ->assertDontSee('Draft Product');
    }

    public function test_product_detail_renders_published_product(): void
    {
        $product = MallMirrorProduct::factory()->create([
            'product_name' => 'Public Product',
            'slug' => 'public-product',
            'description' => 'A published product.',
            'price' => 75,
            'currency' => 'USD',
            'attributes' => ['sku' => 'PUB-001'],
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/products/'.$product->slug)
            ->assertOk()
            ->assertSee('Public Product')
            ->assertSee('A published product.')
            ->assertSee('USD 75.00')
            ->assertSee('PUB-001');
    }

    public function test_product_detail_hides_unpublished_product(): void
    {
        $product = MallMirrorProduct::factory()->create([
            'slug' => 'hidden-product',
            'mirror_status' => 'needs_review',
        ]);

        $this->get('/mall/products/'.$product->slug)
            ->assertNotFound();
    }
}
