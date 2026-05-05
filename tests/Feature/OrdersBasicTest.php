<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Modules\Core\Actions\CreateOrderFromCart;
use App\Modules\Core\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_can_become_draft_order_with_calculated_totals(): void
    {
        $cart = Cart::factory()->create(['currency_code' => 'USD']);
        $product = Product::factory()->create([
            'organization_id' => $cart->organization_id,
            'site_id' => $cart->site_id,
            'name' => 'Service Package',
            'sku' => 'SERV-1',
            'price' => 50,
        ]);
        app(CartService::class)->addProduct($cart, $product, 2);

        $order = app(CreateOrderFromCart::class)($cart, [
            'name' => 'Customer',
            'email' => 'customer@example.test',
        ]);

        $this->assertSame('draft', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('100.00', $order->subtotal);
        $this->assertSame('100.00', $order->total);
        $this->assertSame(1, $order->items()->count());
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'name' => 'Service Package',
            'sku' => 'SERV-1',
            'quantity' => 2,
        ]);
    }
}
