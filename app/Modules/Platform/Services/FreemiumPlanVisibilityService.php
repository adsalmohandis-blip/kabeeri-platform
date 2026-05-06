<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;
use App\Models\Plan;

class FreemiumPlanVisibilityService
{
    public function __construct(private readonly PlanResolverService $resolver) {}

    /**
     * @return array<string, mixed>
     */
    public function summaryFor(?Organization $organization): array
    {
        $currentCode = $this->resolver->resolveCode($organization);

        return [
            'current_plan' => $currentCode,
            'plans' => Plan::query()
                ->with('entitlements')
                ->where('is_active', true)
                ->where('is_public', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Plan $plan): array => [
                    'code' => $plan->code,
                    'name' => $plan->name,
                    'tier' => $plan->tier,
                    'price_cents' => $plan->price_cents,
                    'currency_code' => $plan->currency_code,
                    'billing_interval' => $plan->billing_interval,
                    'is_current' => $plan->code === $currentCode,
                    'highlights' => $this->highlights($plan),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return list<string>
     */
    private function highlights(Plan $plan): array
    {
        $entitlements = $plan->entitlements->keyBy('key');

        return array_values(array_filter([
            $entitlements->has('max_apps') ? 'Apps: '.($entitlements['max_apps']->limit_value ?? 'Custom') : null,
            $entitlements->has('max_pages') ? 'Pages: '.($entitlements['max_pages']->limit_value ?? 'Custom') : null,
            $entitlements->has('max_storage_mb') ? 'Storage MB: '.($entitlements['max_storage_mb']->limit_value ?? 'Custom') : null,
            $entitlements->has('support_level') ? 'Support: '.$entitlements['support_level']->string_value : null,
        ]));
    }
}
