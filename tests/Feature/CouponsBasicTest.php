<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Modules\Core\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_validates_date_status_and_usage_limit(): void
    {
        $service = app(CouponService::class);

        $this->assertTrue($service->isValid(Coupon::factory()->create()));
        $this->assertFalse($service->isValid(Coupon::factory()->create(['status' => 'inactive'])));
        $this->assertFalse($service->isValid(Coupon::factory()->create(['starts_at' => now()->addDay()])));
        $this->assertFalse($service->isValid(Coupon::factory()->create(['usage_limit' => 1, 'used_count' => 1])));
    }

    public function test_coupon_can_apply_fixed_and_percent_discounts(): void
    {
        $service = app(CouponService::class);
        $fixed = Coupon::factory()->create(['discount_type' => 'fixed', 'discount_value' => 15]);
        $percent = Coupon::factory()->create(['discount_type' => 'percent', 'discount_value' => 10]);

        $this->assertSame(85.0, $service->applyToAmount($fixed, 100));
        $this->assertSame(90.0, $service->applyToAmount($percent, 100));
    }
}
