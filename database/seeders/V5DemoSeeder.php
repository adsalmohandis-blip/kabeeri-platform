<?php

namespace Database\Seeders;

use App\Models\BillingUsageRecord;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\ErpProService;
use App\Modules\Platform\Services\IntegrationHubService;
use App\Modules\Rabet\Services\CommissionPayoutService;
use Illuminate\Database\Seeder;

class V5DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->first()
            ?? Organization::query()->first();

        if (! $organization) {
            return;
        }

        if (BillingUsageRecord::query()
            ->where('organization_id', $organization->id)
            ->where('module_key', 'integration_hub')
            ->where('metadata->source', 'v5_demo')
            ->exists()) {
            return;
        }

        $erp = app(ErpProService::class);
        $opportunity = $erp->createOpportunity($organization, [
            'name' => 'V5 Demo ERP Pro Opportunity',
            'expected_value' => 25000,
            'probability' => 40,
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);
        $erp->createDraftContract($organization, [
            'contract_number' => 'CLM-V5-DEMO-001',
            'title' => 'V5 Demo Contract',
            'contractable_type' => $opportunity->getMorphClass(),
            'contractable_id' => $opportunity->id,
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);
        $erp->openTicket($organization, [
            'ticket_number' => 'HD-V5-DEMO-001',
            'subject' => 'V5 Demo Helpdesk Ticket',
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);

        $integration = app(IntegrationHubService::class);
        $connector = $integration->registerConnector($organization, [
            'key' => 'v5-demo-connector',
            'name' => 'V5 Demo Connector',
            'provider' => 'demo',
            'status' => 'draft',
            'capabilities' => ['preview_import'],
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);
        $integration->storeCredentialReference($organization, $connector, [
            'vault_reference' => 'vault://demo/v5-connector',
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);
        $job = $integration->queuePreviewSync($organization, $connector, ['scope' => 'catalog_preview']);
        $integration->log($organization, $job, 'V5 demo preview sync queued.');

        $commission = app(CommissionPayoutService::class);
        $plan = $commission->createPlan($organization, 'V5 Demo Referral Plan', 0.10);
        $event = $commission->recordEvent($organization, $plan, null, $opportunity, 1000);
        $commission->schedulePayout($organization, null, (float) $event->commission_amount);

        BillingUsageRecord::query()->create([
            'organization_id' => $organization->id,
            'module_key' => 'integration_hub',
            'meter_key' => 'preview_sync_jobs',
            'quantity' => 1,
            'usage_date' => now()->toDateString(),
            'billable_type' => $job->getMorphClass(),
            'billable_id' => $job->id,
            'metadata' => ['seeded' => true, 'source' => 'v5_demo'],
        ]);
    }
}
