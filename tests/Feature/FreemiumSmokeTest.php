<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\Site;
use App\Modules\Platform\Services\FairUseGuardService;
use App\Modules\Platform\Services\UpgradeTriggerService;
use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreemiumSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_freemium_schema_seed_services_and_upgrade_triggers_work_together(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'free']);
        Site::factory()->create(['organization_id' => $organization->id]);

        $this->assertDatabaseHas('plans', ['code' => 'free', 'name' => 'Free / Community']);
        $this->assertDatabaseHas('plan_entitlements', ['key' => 'max_apps', 'limit_value' => 1]);
        $this->assertSame(14, Plan::query()->where('code', 'free')->firstOrFail()->entitlements()->count());

        $upgrade = app(UpgradeTriggerService::class)->forAttempt($organization, 'can_use_custom_domain');
        $this->assertSame('upgrade_required', $upgrade['type']);
        $this->assertSame('starter', $upgrade['recommended_plan']);

        $fairUse = app(FairUseGuardService::class)->inspect($organization);
        $this->assertSame('warning', $fairUse['status']);
        $this->assertSame('max_apps', $fairUse['warnings'][0]['key']);
    }
}
