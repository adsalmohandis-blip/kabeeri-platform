<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Modules\Platform\Services\FreemiumDefaults;
use Illuminate\Database\Seeder;

class FreemiumSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FreemiumDefaults::plans() as $code => $definition) {
            $plan = Plan::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $definition['name'],
                    'tier' => $definition['tier'],
                    'price_cents' => $definition['price_cents'],
                    'currency_code' => 'USD',
                    'billing_interval' => $code === 'free' || $code === 'enterprise' ? null : 'monthly',
                    'is_public' => true,
                    'is_active' => true,
                    'sort_order' => $definition['sort_order'],
                    'metadata' => ['seeded' => true, 'source' => 'freemium_defaults'],
                ],
            );

            foreach ($definition['entitlements'] as $key => $entitlement) {
                $plan->entitlements()->updateOrCreate(
                    ['key' => $key],
                    [
                        'value_type' => $entitlement['value_type'],
                        'limit_value' => $entitlement['limit_value'],
                        'bool_value' => $entitlement['bool_value'],
                        'string_value' => $entitlement['string_value'],
                        'reset_period' => str_contains($key, 'monthly') ? 'monthly' : null,
                        'behavior' => $entitlement['behavior'],
                        'upgrade_plan_code' => $entitlement['upgrade_plan_code'],
                        'metadata' => ['seeded' => true, 'source' => 'freemium_defaults'],
                    ],
                );
            }
        }
    }
}
