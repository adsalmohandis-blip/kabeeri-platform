<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contract;
use App\Models\ErpProOpportunity;
use App\Models\HelpdeskTicket;
use App\Models\Organization;

class ErpProService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createOpportunity(Organization $organization, array $attributes): ErpProOpportunity
    {
        return ErpProOpportunity::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'open',
            'priority' => $attributes['priority'] ?? 'normal',
            'currency_code' => $attributes['currency_code'] ?? 'EGP',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraftContract(Organization $organization, array $attributes): Contract
    {
        return Contract::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'contract_number' => $attributes['contract_number'] ?? $this->nextNumber($organization, Contract::class, 'contract_number', 'CLM'),
            'status' => 'draft',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function openTicket(Organization $organization, array $attributes): HelpdeskTicket
    {
        return HelpdeskTicket::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'ticket_number' => $attributes['ticket_number'] ?? $this->nextNumber($organization, HelpdeskTicket::class, 'ticket_number', 'HD'),
            'status' => $attributes['status'] ?? 'open',
            'priority' => $attributes['priority'] ?? 'normal',
        ]);
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    private function nextNumber(Organization $organization, string $model, string $column, string $prefix): string
    {
        $next = $model::query()->where('organization_id', $organization->id)->count() + 1;

        return $prefix.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
