<?php

namespace App\Modules\Core\Services;

use App\Models\FeatureFlag;
use App\Models\FeatureFlagOverride;

class FeatureFlagService
{
    public function isEnabled(
        string $key,
        ?int $organizationId = null,
        ?string $scopeType = null,
        ?int $scopeId = null,
    ): bool {
        $flag = FeatureFlag::query()->where('key', $key)->first();

        if (! $flag || $flag->status !== 'active') {
            return false;
        }

        $override = $this->resolveOverride(
            featureFlagId: $flag->id,
            organizationId: $organizationId,
            scopeType: $scopeType,
            scopeId: $scopeId,
        );

        return $override?->value ?? $flag->default_value;
    }

    protected function resolveOverride(
        int $featureFlagId,
        ?int $organizationId,
        ?string $scopeType,
        ?int $scopeId,
    ): ?FeatureFlagOverride {
        if ($scopeType !== null && $scopeId !== null && $organizationId !== null) {
            $exactScoped = FeatureFlagOverride::query()
                ->where('feature_flag_id', $featureFlagId)
                ->where('organization_id', $organizationId)
                ->where('scope_type', $scopeType)
                ->where('scope_id', $scopeId)
                ->first();

            if ($exactScoped) {
                return $exactScoped;
            }
        }

        if ($scopeType !== null && $scopeId !== null) {
            $scopedGlobal = FeatureFlagOverride::query()
                ->where('feature_flag_id', $featureFlagId)
                ->whereNull('organization_id')
                ->where('scope_type', $scopeType)
                ->where('scope_id', $scopeId)
                ->first();

            if ($scopedGlobal) {
                return $scopedGlobal;
            }
        }

        if ($organizationId !== null) {
            $organizationOverride = FeatureFlagOverride::query()
                ->where('feature_flag_id', $featureFlagId)
                ->where('organization_id', $organizationId)
                ->whereNull('scope_type')
                ->whereNull('scope_id')
                ->first();

            if ($organizationOverride) {
                return $organizationOverride;
            }
        }

        return FeatureFlagOverride::query()
            ->where('feature_flag_id', $featureFlagId)
            ->whereNull('organization_id')
            ->whereNull('scope_type')
            ->whereNull('scope_id')
            ->first();
    }
}
