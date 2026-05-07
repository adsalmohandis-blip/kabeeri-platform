<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;

class UpgradeTriggerService
{
    public function __construct(private readonly EntitlementService $entitlements) {}

    /**
     * @return array<string, mixed>|null
     */
    public function forAttempt(Organization $organization, string $key, int $requested = 1, ?int $siteId = null): ?array
    {
        return $this->forResult($this->entitlements->check($organization, $key, $requested, $siteId));
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>|null
     */
    public function forResult(array $result): ?array
    {
        if (! $result['allowed']) {
            return [
                'type' => 'upgrade_required',
                'entitlement_key' => $result['key'],
                'current_plan' => $result['plan_code'],
                'recommended_plan' => $this->recommendedPlan((string) $result['key'], (string) $result['plan_code']),
                'reason' => $result['reason'],
                'message' => 'This action needs a higher kabeeri plan or an explicit entitlement override.',
                'usage' => $result['usage'],
                'limit' => $result['limit'],
            ];
        }

        if ($result['limit'] !== null && $result['limit'] > 0) {
            $projectedUsage = (int) $result['usage'] + max(0, (int) $result['requested']);
            $ratio = $projectedUsage / (int) $result['limit'];

            if ($ratio >= 0.8) {
                return [
                    'type' => 'upgrade_suggested',
                    'entitlement_key' => $result['key'],
                    'current_plan' => $result['plan_code'],
                    'recommended_plan' => $this->recommendedPlan((string) $result['key'], (string) $result['plan_code']),
                    'reason' => 'near_limit',
                    'message' => 'Usage is approaching the current plan limit.',
                    'usage' => $projectedUsage,
                    'limit' => $result['limit'],
                ];
            }
        }

        return null;
    }

    private function recommendedPlan(string $key, string $currentPlan): string
    {
        if ($currentPlan === 'enterprise') {
            return 'enterprise';
        }

        return match ($key) {
            'commerce_products_max', 'team_members_max' => 'business',
            'max_apps' => $currentPlan === 'free' ? 'starter' : 'business',
            'can_use_custom_domain', 'can_remove_branding', 'can_install_pro_themes', 'can_install_paid_plugins' => 'starter',
            default => $currentPlan === 'free' ? 'starter' : 'business',
        };
    }
}
