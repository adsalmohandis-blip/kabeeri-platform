<?php

namespace Tests\Feature;

use App\Models\AgencyPartnerProfile;
use App\Modules\Rabet\Services\AgencyDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyDashboardFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_dashboard_snapshot_captures_basic_profile_metrics(): void
    {
        $profile = AgencyPartnerProfile::factory()->create([
            'status' => 'active',
            'accreditation_status' => 'accredited',
            'accreditation_level' => 'gold',
            'service_categories' => ['implementation', 'migration'],
            'regions' => ['EG', 'SA'],
            'languages' => ['ar', 'en'],
        ]);

        $snapshot = app(AgencyDashboardService::class)->snapshot($profile);

        $this->assertSame($profile->organization_id, $snapshot->organization_id);
        $this->assertSame($profile->id, $snapshot->agency_partner_profile_id);
        $this->assertSame('active', $snapshot->metrics['status']);
        $this->assertSame('gold', $snapshot->metrics['accreditation_level']);
        $this->assertSame(2, $snapshot->metrics['service_categories_count']);
        $this->assertSame([], $snapshot->alerts);
        $this->assertNotNull($snapshot->generated_at);
    }

    public function test_agency_dashboard_snapshot_warns_when_accreditation_incomplete(): void
    {
        $profile = AgencyPartnerProfile::factory()->create([
            'accreditation_status' => 'submitted',
        ]);

        $snapshot = app(AgencyDashboardService::class)->snapshot($profile);

        $this->assertSame('accreditation', $snapshot->alerts[0]['type']);
    }
}
