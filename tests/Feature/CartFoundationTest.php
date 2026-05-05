<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Modules\Core\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_product_to_cart_updates_quantity(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create([
            'organization_id' => $cart->organization_id,
            'site_id' => $cart->site_id,
            'price' => 25,
            'currency_code' => 'USD',
        ]);
        $service = app(CartService::class);

        $service->addProduct($cart, $product, 1);
        $item = $service->addProduct($cart, $product, 2);

        $this->assertSame(3, $item->quantity);
        $this->assertSame('25.00', $item->unit_price);
        $this->assertSame(1, $cart->items()->count());
    }

    public function test_variant_can_override_cart_item_price(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['price' => 25]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 30,
        ]);

        $item = app(CartService::class)->addProduct($cart, $product, 1, $variant);

        $this->assertSame('30.00', $item->unit_price);
        $this->assertSame($variant->id, $item->product_variant_id);
    }

    public function test_update_quantity_and_remove_item(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();
        $service = app(CartService::class);
        $item = $service->addProduct($cart, $product, 1);

        $this->assertSame(5, $service->updateQuantity($item, 5)?->quantity);
        $service->removeItem($item->refresh());

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
