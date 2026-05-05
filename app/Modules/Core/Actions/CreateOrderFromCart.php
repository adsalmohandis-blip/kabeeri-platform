<?php

namespace App\Modules\Core\Actions;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CreateOrderFromCart
{
    /**
     * @param  array<string, mixed>  $customer
     */
    public function __invoke(Cart $cart, array $customer = []): Order
    {
        return DB::transaction(function () use ($cart, $customer): Order {
            $cart->load('items');
            $subtotal = $cart->items->sum(fn ($item): float => (float) $item->unit_price * $item->quantity);

            $order = Order::query()->create([
                'organization_id' => $cart->organization_id,
                'site_id' => $cart->site_id,
                'user_id' => $cart->user_id,
                'customer_name' => $customer['name'] ?? null,
                'customer_email' => $customer['email'] ?? null,
                'customer_phone' => $customer['phone'] ?? null,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'currency_code' => $cart->currency_code,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'total' => $subtotal,
                'metadata' => ['cart_id' => $cart->id],
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product_id ? Product::query()->find($item->product_id) : null;
                $variant = $item->product_variant_id ? ProductVariant::query()->find($item->product_variant_id) : null;

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'name' => $product?->name ?? 'Product',
                    'sku' => $variant?->sku ?? $product?->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price ?? 0,
                    'total' => (float) ($item->unit_price ?? 0) * $item->quantity,
                    'metadata' => ['cart_item_id' => $item->id],
                ]);
            }

            return $order->refresh();
        });
    }
}
