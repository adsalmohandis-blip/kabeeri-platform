<?php

namespace App\Modules\Platform\Services;

use App\Models\ContentEntry;
use App\Models\EntitlementOverride;
use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Plan;
use App\Models\PlanEntitlement;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Auth\Access\AuthorizationException;

class EntitlementService
{
    public function __construct(
        private readonly PlanResolverService $plans,
        private readonly UsageMeterService $usage,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function check(Organization|int|null $organization, string $key, int $requested = 1, ?int $siteId = null): array
    {
        $organization = $this->organization($organization);
        $plan = $this->plans->resolve($organization);
        $planCode = $plan?->code ?? $this->plans->resolveCode($organization);
        $entitlement = $this->activeOverride($organization?->id, $siteId, $key)
            ?? $this->planEntitlement($plan, $key)
            ?? $this->fallbackEntitlement($planCode, $key);

        if (! $entitlement) {
            return $this->result(false, $key, $planCode, 'missing_entitlement', null, null, $requested, 'block');
        }

        $valueType = (string) $entitlement['value_type'];
        $limit = $entitlement['limit_value'];
        $bool = $entitlement['bool_value'];
        $string = $entitlement['string_value'];
        $behavior = (string) ($entitlement['behavior'] ?? 'allow');
        $currentUsage = $this->usageFor($organization, $key, $siteId);

        if ($behavior === 'block') {
            return $this->result(false, $key, $planCode, 'blocked_by_entitlement', $limit, $currentUsage, $requested, $behavior, $string, $bool);
        }

        if ($valueType === 'boolean') {
            return $this->result((bool) $bool, $key, $planCode, $bool ? 'allowed' : 'upgrade_required', null, $currentUsage, $requested, $behavior, $string, $bool);
        }

        if ($valueType === 'string') {
            return $this->result(true, $key, $planCode, 'allowed', null, $currentUsage, $requested, $behavior, $string, $bool);
        }

        if ($limit === null) {
            return $this->result(true, $key, $planCode, 'allowed_unlimited', null, $currentUsage, $requested, $behavior, $string, $bool);
        }

        $allowed = ($currentUsage + max(0, $requested)) <= (int) $limit;

        return $this->result($allowed, $key, $planCode, $allowed ? 'allowed' : 'limit_exceeded', (int) $limit, $currentUsage, $requested, $behavior, $string, $bool);
    }

    public function allows(Organization|int|null $organization, string $key, int $requested = 1, ?int $siteId = null): bool
    {
        return (bool) $this->check($organization, $key, $requested, $siteId)['allowed'];
    }

    /**
     * @throws AuthorizationException
     */
    public function assertAllowed(Organization|int|null $organization, string $key, int $requested = 1, ?int $siteId = null): void
    {
        $result = $this->check($organization, $key, $requested, $siteId);

        if (! $result['allowed']) {
            throw new AuthorizationException('Entitlement denied: '.$key.' ('.$result['reason'].')');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function canCreateApp(Organization $organization): array
    {
        $base = $this->check($organization, 'can_create_app', 1);

        if (! $base['allowed']) {
            return $base;
        }

        return $this->check($organization, 'max_apps', 1);
    }

    /**
     * @return array<string, mixed>
     */
    public function canInstallTheme(Organization $organization, string $priceType, ?int $siteId = null): array
    {
        $key = in_array($priceType, ['free', 'community'], true)
            ? 'can_install_free_themes'
            : 'can_install_pro_themes';

        return $this->check($organization, $key, 1, $siteId);
    }

    /**
     * @return array<string, mixed>
     */
    public function canInstallPlugin(Organization $organization, string $priceType, ?int $siteId = null): array
    {
        $key = in_array($priceType, ['free', 'community'], true)
            ? 'can_install_free_plugins'
            : 'can_install_paid_plugins';

        return $this->check($organization, $key, 1, $siteId);
    }

    public function usageFor(?Organization $organization, string $key, ?int $siteId = null): int
    {
        if (! $organization) {
            return 0;
        }

        return match ($key) {
            'max_apps' => Site::query()->where('organization_id', $organization->id)->count(),
            'max_pages' => ContentEntry::query()->where('organization_id', $organization->id)->count(),
            'max_storage_mb' => (int) ceil(((int) MediaAsset::query()->where('organization_id', $organization->id)->sum('size_bytes')) / 1024 / 1024),
            'commerce_products_max' => Product::query()->where('organization_id', $organization->id)->count(),
            'team_members_max' => OrganizationMembership::query()->where('organization_id', $organization->id)->where('status', 'active')->count(),
            default => $this->usage->currentMonthlyUsage($organization, $key, $siteId),
        };
    }

    private function organization(Organization|int|null $organization): ?Organization
    {
        if ($organization instanceof Organization || $organization === null) {
            return $organization;
        }

        return Organization::query()->find($organization);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function planEntitlement(?Plan $plan, string $key): ?array
    {
        $entitlement = $plan?->entitlements->firstWhere('key', $key);

        return $entitlement instanceof PlanEntitlement ? $this->normalize($entitlement) : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function activeOverride(?int $organizationId, ?int $siteId, string $key): ?array
    {
        $now = now();

        if ($siteId !== null) {
            $siteOverride = EntitlementOverride::query()
                ->where('site_id', $siteId)
                ->where('key', $key)
                ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', $now))
                ->latest('id')
                ->first();

            if ($siteOverride instanceof EntitlementOverride) {
                return $this->normalize($siteOverride);
            }
        }

        if ($organizationId === null) {
            return null;
        }

        $override = EntitlementOverride::query()
            ->where('organization_id', $organizationId)
            ->whereNull('site_id')
            ->where('key', $key)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', $now))
            ->latest('id')
            ->first();

        return $override instanceof EntitlementOverride ? $this->normalize($override) : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalize(PlanEntitlement|EntitlementOverride $entitlement): array
    {
        return [
            'value_type' => $entitlement->value_type,
            'limit_value' => $entitlement->limit_value,
            'bool_value' => $entitlement->bool_value,
            'string_value' => $entitlement->string_value,
            'behavior' => $entitlement->behavior,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fallbackEntitlement(string $planCode, string $key): ?array
    {
        $plans = FreemiumDefaults::plans();
        $entitlements = $plans[$planCode]['entitlements'] ?? $plans[PlanResolverService::DEFAULT_PLAN_CODE]['entitlements'];

        return $entitlements[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    private function result(
        bool $allowed,
        string $key,
        string $planCode,
        string $reason,
        ?int $limit,
        ?int $usage,
        int $requested,
        string $behavior,
        ?string $stringValue = null,
        ?bool $boolValue = null,
    ): array {
        return [
            'allowed' => $allowed,
            'key' => $key,
            'plan_code' => $planCode,
            'reason' => $reason,
            'limit' => $limit,
            'usage' => $usage ?? 0,
            'requested' => $requested,
            'remaining' => $limit === null ? null : max(0, $limit - (($usage ?? 0) + max(0, $requested))),
            'behavior' => $behavior,
            'string_value' => $stringValue,
            'bool_value' => $boolValue,
        ];
    }
}
