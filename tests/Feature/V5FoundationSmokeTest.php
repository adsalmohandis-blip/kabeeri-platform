<?php

namespace Tests\Feature;

use App\Models\BillingUsageRecord;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\ErpProService;
use App\Modules\Platform\Services\IntegrationHubService;
use App\Modules\Rabet\Services\CommissionPayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class V5FoundationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_v5_start_pack_tables_are_available(): void
    {
        foreach ([
            'erp_pro_opportunities',
            'contracts',
            'helpdesk_tickets',
            'commission_plans',
            'commission_events',
            'partner_payouts',
            'integration_connectors',
            'integration_credentials',
            'external_object_links',
            'integration_sync_jobs',
            'integration_sync_logs',
            'billing_usage_records',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing V5 table [{$table}].");
        }
    }

    public function test_v5_erp_integration_commission_and_usage_flow(): void
    {
        $organization = Organization::factory()->create();

        $opportunity = app(ErpProService::class)->createOpportunity($organization, [
            'name' => 'V5 Smoke Opportunity',
            'expected_value' => 5000,
        ]);
        $contract = app(ErpProService::class)->createDraftContract($organization, [
            'title' => 'V5 Smoke Contract',
            'contractable_type' => $opportunity->getMorphClass(),
            'contractable_id' => $opportunity->id,
        ]);
        $ticket = app(ErpProService::class)->openTicket($organization, [
            'subject' => 'V5 Smoke Ticket',
        ]);

        $hub = app(IntegrationHubService::class);
        $connector = $hub->registerConnector($organization, [
            'key' => 'smoke-connector',
            'name' => 'Smoke Connector',
            'provider' => 'demo',
        ]);
        $credential = $hub->storeCredentialReference($organization, $connector, [
            'vault_reference' => 'vault://smoke/connector',
        ]);
        $link = $hub->linkExternalObject($organization, $connector, $opportunity, 'deal', 'EXT-1');
        $job = $hub->queuePreviewSync($organization, $connector, ['object' => 'deal']);
        $log = $hub->log($organization, $job, 'Queued preview sync.');

        $commission = app(CommissionPayoutService::class);
        $plan = $commission->createPlan($organization, 'Smoke Plan', 0.10);
        $event = $commission->recordEvent($organization, $plan, null, $opportunity, 1000);
        $payout = $commission->schedulePayout($organization, null, (float) $event->commission_amount);

        $usage = BillingUsageRecord::query()->create([
            'organization_id' => $organization->id,
            'module_key' => 'integration_hub',
            'meter_key' => 'preview_sync_jobs',
            'quantity' => 1,
            'usage_date' => now()->toDateString(),
            'billable_type' => $job->getMorphClass(),
            'billable_id' => $job->id,
        ]);

        $this->assertSame('open', $opportunity->status);
        $this->assertSame('draft', $contract->status);
        $this->assertSame('open', $ticket->status);
        $this->assertSame('vault://smoke/connector', $credential->vault_reference);
        $this->assertSame('preview', $link->sync_direction);
        $this->assertSame('queued', $job->status);
        $this->assertSame('Queued preview sync.', $log->message);
        $this->assertSame('100.00', $event->commission_amount);
        $this->assertSame('pending_review', $payout->status);
        $this->assertSame('integration_hub', $usage->module_key);
    }

    public function test_integration_credentials_reject_raw_secrets(): void
    {
        $organization = Organization::factory()->create();
        $connector = app(IntegrationHubService::class)->registerConnector($organization, [
            'key' => 'unsafe-connector',
            'name' => 'Unsafe Connector',
            'provider' => 'demo',
        ]);

        $this->expectException(ValidationException::class);

        app(IntegrationHubService::class)->storeCredentialReference($organization, $connector, [
            'vault_reference' => 'vault://safe/reference',
            'secret' => 'do-not-store-me',
        ]);
    }

    public function test_v5_keeps_users_table_tenant_clean(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'organization_id'));
        $this->assertFalse(Schema::hasColumn('users', 'company_id'));
        $this->assertFalse(Schema::hasColumn('users', 'site_id'));
        $this->assertFalse(Schema::hasColumn('users', 'role'));
    }
}
