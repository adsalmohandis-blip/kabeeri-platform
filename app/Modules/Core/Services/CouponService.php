<?php

namespace App\Modules\Core\Services;

use App\Models\Coupon;

class CouponService
{
    public function isValid(Coupon $coupon): bool
    {
        if ($coupon->status !== 'active') {
            return false;
        }

        if ($coupon->starts_at !== null && $coupon->starts_at->isFuture()) {
            return false;
        }

        if ($coupon->ends_at !== null && $coupon->ends_at->isPast()) {
            return false;
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        return true;
    }

    public function discountFor(Coupon $coupon, float $amount): float
    {
        if (! $this->isValid($coupon)) {
            return 0.0;
        }

        $discount = match ($coupon->discount_type) {
            'percent' => $amount * ((float) $coupon->discount_value / 100),
            'fixed' => (float) $coupon->discount_value,
            default => 0.0,
        };

        return round(min($discount, $amount), 2);
    }

    public function applyToAmount(Coupon $coupon, float $amount): float
    {
        return round(max(0, $amount - $this->discountFor($coupon, $amount)), 2);
    }
}
