<?php

namespace App\Modules\Core\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    public function addProduct(Cart $cart, Product $product, int $quantity = 1, ?ProductVariant $variant = null): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
        ]);

        $item->fill([
            'quantity' => ($item->exists ? $item->quantity : 0) + $quantity,
            'unit_price' => $variant?->price ?? $product->price,
            'metadata' => ['currency_code' => $product->currency_code],
        ])->save();

        return $item->refresh();
    }

    public function updateQuantity(CartItem $item, int $quantity): ?CartItem
    {
        if ($quantity <= 0) {
            $item->delete();

            return null;
        }

        $item->forceFill(['quantity' => $quantity])->save();

        return $item->refresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }
}
