<?php

namespace App\Modules\Rabet\Services;

use App\Models\CommissionEvent;
use App\Models\CommissionPlan;
use App\Models\Organization;
use App\Models\PartnerPayout;
use App\Models\PartnerStorefront;
use Illuminate\Database\Eloquent\Model;

class CommissionPayoutService
{
    public function createPlan(Organization $organization, string $name, float $rate): CommissionPlan
    {
        return CommissionPlan::query()->create([
            'organization_id' => $organization->id,
            'name' => $name,
            'status' => 'draft',
            'default_rate' => $rate,
        ]);
    }

    public function recordEvent(Organization $organization, CommissionPlan $plan, ?PartnerStorefront $storefront, ?Model $source, float $baseAmount): CommissionEvent
    {
        $rate = (float) $plan->default_rate;

        return CommissionEvent::query()->create([
            'organization_id' => $organization->id,
            'commission_plan_id' => $plan->id,
            'partner_storefront_id' => $storefront?->id,
            'commissionable_type' => $source?->getMorphClass(),
            'commissionable_id' => $source?->getKey(),
            'status' => 'pending',
            'base_amount' => $baseAmount,
            'commission_rate' => $rate,
            'commission_amount' => round($baseAmount * $rate, 2),
            'earned_at' => now(),
        ]);
    }

    public function schedulePayout(Organization $organization, ?PartnerStorefront $storefront, float $amount): PartnerPayout
    {
        $next = PartnerPayout::query()->where('organization_id', $organization->id)->count() + 1;

        return PartnerPayout::query()->create([
            'organization_id' => $organization->id,
            'partner_storefront_id' => $storefront?->id,
            'payout_number' => 'POUT-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'status' => 'pending_review',
            'amount' => $amount,
            'risk_checks' => ['status' => 'pending'],
        ]);
    }
}
