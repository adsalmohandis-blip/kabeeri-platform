<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Cart> */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => ['organization_id' => $attributes['organization_id']]),
            'user_id' => null,
            'session_id' => fake()->uuid(),
            'status' => 'active',
            'currency_code' => 'USD',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
