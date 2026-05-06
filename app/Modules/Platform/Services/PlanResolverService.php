<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;
use App\Models\Plan;

class PlanResolverService
{
    public const DEFAULT_PLAN_CODE = 'free';

    public function resolve(?Organization $organization): ?Plan
    {
        $code = $this->resolveCode($organization);

        return Plan::query()
            ->with('entitlements')
            ->where('code', $code)
            ->where('is_active', true)
            ->first()
            ?? Plan::query()
                ->with('entitlements')
                ->where('code', self::DEFAULT_PLAN_CODE)
                ->where('is_active', true)
                ->first();
    }

    public function resolveCode(?Organization $organization): string
    {
        $code = trim((string) ($organization?->plan_code ?: self::DEFAULT_PLAN_CODE));

        return $code === '' ? self::DEFAULT_PLAN_CODE : $code;
    }
}
