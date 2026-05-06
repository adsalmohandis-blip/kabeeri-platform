<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;

class FairUseGuardService
{
    public function __construct(private readonly EntitlementService $entitlements) {}

    /**
     * @return array<string, mixed>
     */
    public function inspect(Organization $organization): array
    {
        $warnings = [];
        $violations = [];

        foreach (['max_apps', 'max_pages', 'max_storage_mb', 'commerce_products_max', 'ai_credits_monthly'] as $key) {
            $result = $this->entitlements->check($organization, $key, 0);

            if ($result['limit'] === null) {
                continue;
            }

            $ratio = $result['limit'] > 0 ? ((int) $result['usage'] / (int) $result['limit']) : 0;

            if (! $result['allowed'] || $ratio > 1) {
                $violations[] = $result + ['ratio' => $ratio];
            } elseif ($ratio >= 0.9) {
                $warnings[] = $result + ['ratio' => $ratio];
            }
        }

        return [
            'status' => $violations !== [] ? 'violation' : ($warnings !== [] ? 'warning' : 'clear'),
            'warnings' => $warnings,
            'violations' => $violations,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function withinLimit(Organization $organization, string $key, int $requested = 1): array
    {
        $result = $this->entitlements->check($organization, $key, $requested);

        return $result + ['fair_use_status' => $result['allowed'] ? 'clear' : 'limit_exceeded'];
    }
}
