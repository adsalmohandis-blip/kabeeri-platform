<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Coupon> */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => ['organization_id' => $attributes['organization_id']]),
            'code' => strtoupper(fake()->unique()->bothify('SAVE##')),
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'usage_limit' => null,
            'used_count' => 0,
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
