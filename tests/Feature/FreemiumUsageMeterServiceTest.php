<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\Platform\Services\EntitlementService;
use App\Modules\Platform\Services\UsageMeterService;
use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreemiumUsageMeterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_meter_accumulates_current_month_usage(): void
    {
        $organization = Organization::factory()->create(['plan_code' => 'starter']);

        app(UsageMeterService::class)->increment($organization, 'ai_credits_monthly', 10, metadata: ['source' => 'test']);
        app(UsageMeterService::class)->increment($organization, 'ai_credits_monthly', 5, metadata: ['source' => 'test']);

        $this->assertDatabaseHas('usage_records', [
            'organization_id' => $organization->id,
            'entitlement_key' => 'ai_credits_monthly',
            'quantity' => 15,
        ]);

        $this->assertSame(15, app(UsageMeterService::class)->currentMonthlyUsage($organization, 'ai_credits_monthly'));
    }

    public function test_usage_meter_feeds_entitlement_limit_checks(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'free']);
        app(UsageMeterService::class)->increment($organization, 'ai_credits_monthly', 1);

        $result = app(EntitlementService::class)->check($organization, 'ai_credits_monthly', 1);

        $this->assertFalse($result['allowed']);
        $this->assertSame(0, $result['limit']);
        $this->assertSame(1, $result['usage']);
    }
}
