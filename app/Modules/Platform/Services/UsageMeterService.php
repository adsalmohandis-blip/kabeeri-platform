<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;
use App\Models\UsageRecord;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class UsageMeterService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function increment(
        Organization|int|null $organization,
        string $entitlementKey,
        int $quantity = 1,
        ?CarbonInterface $periodStart = null,
        ?CarbonInterface $periodEnd = null,
        ?int $siteId = null,
        ?int $userId = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        array $metadata = [],
    ): UsageRecord {
        [$start, $end] = $this->period($periodStart, $periodEnd);
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;

        $record = UsageRecord::query()
            ->where('entitlement_key', $entitlementKey)
            ->whereDate('period_start', $start->toDateString())
            ->whereDate('period_end', $end->toDateString())
            ->when($organizationId === null, fn ($query) => $query->whereNull('organization_id'), fn ($query) => $query->where('organization_id', $organizationId))
            ->when($siteId === null, fn ($query) => $query->whereNull('site_id'), fn ($query) => $query->where('site_id', $siteId))
            ->when($userId === null, fn ($query) => $query->whereNull('user_id'), fn ($query) => $query->where('user_id', $userId))
            ->when($sourceType === null, fn ($query) => $query->whereNull('source_type'), fn ($query) => $query->where('source_type', $sourceType))
            ->when($sourceId === null, fn ($query) => $query->whereNull('source_id'), fn ($query) => $query->where('source_id', $sourceId))
            ->first();

        $record ??= new UsageRecord([
            'organization_id' => $organizationId,
            'site_id' => $siteId,
            'user_id' => $userId,
            'entitlement_key' => $entitlementKey,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        $record->quantity = max(0, (int) $record->quantity + $quantity);
        $record->metadata = array_merge($record->metadata ?? [], $metadata);
        $record->save();

        return $record;
    }

    public function currentMonthlyUsage(Organization|int|null $organization, string $entitlementKey, ?int $siteId = null, ?int $userId = null): int
    {
        [$start, $end] = $this->period();
        $organizationId = $organization instanceof Organization ? $organization->id : $organization;

        return (int) UsageRecord::query()
            ->when($organizationId !== null, fn ($query) => $query->where('organization_id', $organizationId))
            ->when($siteId !== null, fn ($query) => $query->where('site_id', $siteId))
            ->when($userId !== null, fn ($query) => $query->where('user_id', $userId))
            ->where('entitlement_key', $entitlementKey)
            ->whereDate('period_start', $start->toDateString())
            ->whereDate('period_end', $end->toDateString())
            ->sum('quantity');
    }

    /**
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    private function period(?CarbonInterface $periodStart = null, ?CarbonInterface $periodEnd = null): array
    {
        $start = $periodStart ? Carbon::instance($periodStart->toDateTime())->startOfDay() : now()->startOfMonth();
        $end = $periodEnd ? Carbon::instance($periodEnd->toDateTime())->startOfDay() : now()->endOfMonth()->startOfDay();

        return [$start, $end];
    }
}
