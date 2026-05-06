<?php

namespace Tests\Feature;

use App\Filament\Resources\Plans\PlanResource;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use App\Modules\Platform\Services\FreemiumPlanVisibilityService;
use Database\Seeders\FreemiumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreemiumPlanVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_catalog_is_seeded_and_visible_in_filament_for_authenticated_admin(): void
    {
        $this->seed(FreemiumSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertSame(5, PlanResource::getEloquentQuery()->count());
        $this->get(PlanResource::getUrl('index'))->assertOk();
        $this->assertSame(5, Plan::query()->where('is_public', true)->where('is_active', true)->count());
    }

    public function test_plan_visibility_summary_marks_current_organization_plan(): void
    {
        $this->seed(FreemiumSeeder::class);

        $organization = Organization::factory()->create(['plan_code' => 'business']);
        $summary = app(FreemiumPlanVisibilityService::class)->summaryFor($organization);

        $this->assertSame('business', $summary['current_plan']);
        $this->assertTrue(collect($summary['plans'])->firstWhere('code', 'business')['is_current']);
        $this->assertFalse(collect($summary['plans'])->firstWhere('code', 'free')['is_current']);
    }

    public function test_plan_resource_returns_no_records_when_unauthenticated(): void
    {
        $this->seed(FreemiumSeeder::class);

        $this->assertSame(0, PlanResource::getEloquentQuery()->count());
    }
}
