<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItem> */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => null,
            'product_variant_id' => null,
            'name' => fake()->words(2, true),
            'sku' => fake()->bothify('SKU-####'),
            'quantity' => 1,
            'unit_price' => 10,
            'total' => 10,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
