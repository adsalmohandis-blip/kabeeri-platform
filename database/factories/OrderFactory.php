<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => null,
            'user_id' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'currency_code' => 'USD',
            'subtotal' => 0,
            'discount_total' => 0,
            'total' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
