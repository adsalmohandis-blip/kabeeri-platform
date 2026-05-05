<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\LeadScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSourcesAndScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_source_is_tenant_scoped_and_can_have_default_score(): void
    {
        $organization = Organization::factory()->create();

        $source = LeadSource::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Website',
            'slug' => 'website',
            'source_type' => 'website',
            'default_score' => 25,
        ]);

        $this->assertNotNull($source->ulid);
        $this->assertTrue($source->organization->is($organization));
        $this->assertTrue($source->is_active);
        $this->assertSame(25, $source->default_score);
    }

    public function test_lead_can_be_scored_from_source_priority_value_and_contact_data(): void
    {
        $source = LeadSource::factory()->create(['default_score' => 25]);
        $lead = Lead::factory()->create([
            'organization_id' => $source->organization_id,
            'lead_source_id' => $source->id,
            'priority' => 'high',
            'expected_value' => 15000,
            'email' => 'buyer@example.com',
            'phone' => '+201000000000',
            'score' => null,
        ]);

        $scored = app(LeadScoringService::class)->score($lead);

        $this->assertSame(80, $scored->score);
        $this->assertSame([
            'source' => 25,
            'priority' => 20,
            'expected_value' => 15,
            'contact_details' => 20,
        ], $scored->score_breakdown);
        $this->assertNotNull($scored->scored_at);
    }

    public function test_score_is_capped_between_zero_and_one_hundred(): void
    {
        $source = LeadSource::factory()->create(['default_score' => 95]);
        $lead = Lead::factory()->create([
            'organization_id' => $source->organization_id,
            'lead_source_id' => $source->id,
            'priority' => 'urgent',
            'expected_value' => 999999,
            'email' => 'buyer@example.com',
            'phone' => '+201000000000',
        ]);

        $scored = app(LeadScoringService::class)->score($lead);

        $this->assertSame(100, $scored->score);
    }
}
