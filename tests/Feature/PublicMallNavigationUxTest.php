<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMallNavigationUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_mall_home_lists_public_sections_and_published_counts(): void
    {
        MallMirrorBusiness::factory()->create([
            'display_name' => 'Published Business',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Published Product',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Draft Product',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall')
            ->assertOk()
            ->assertSee('KABEERI Mall')
            ->assertSee(route('mall.businesses.index'), false)
            ->assertSee(route('mall.products.index'), false)
            ->assertSee('Business Directory')
            ->assertSee('Products')
            ->assertSee('1 published')
            ->assertDontSee('2 published');
    }

    public function test_mall_section_pages_include_cross_section_navigation(): void
    {
        $this->get('/mall/products')
            ->assertOk()
            ->assertSee('Mall Home')
            ->assertSee('Business Directory')
            ->assertSee('Services')
            ->assertSee('Travel');
    }
}
