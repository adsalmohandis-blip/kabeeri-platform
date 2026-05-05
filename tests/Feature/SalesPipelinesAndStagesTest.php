<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineStage;
use App\Modules\BusinessOperations\Services\SalesPipelineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SalesPipelinesAndStagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_can_have_ordered_stages(): void
    {
        $pipeline = SalesPipeline::factory()->create([
            'name' => 'Default Sales',
            'slug' => 'default-sales',
            'is_default' => true,
        ]);

        $stage = app(SalesPipelineService::class)->addStage($pipeline, 'Qualified', 'qualified', 10, 40);

        $this->assertNotNull($pipeline->ulid);
        $this->assertTrue($stage->pipeline->is($pipeline));
        $this->assertSame(10, $stage->sort_order);
        $this->assertSame(40, $stage->probability);
        $this->assertFalse($stage->is_won);
    }

    public function test_lead_can_move_to_stage_in_same_organization(): void
    {
        $pipeline = SalesPipeline::factory()->create();
        $stage = SalesPipelineStage::factory()->create([
            'sales_pipeline_id' => $pipeline->id,
            'name' => 'Proposal',
            'slug' => 'proposal',
            'sort_order' => 20,
        ]);
        $lead = Lead::factory()->create(['organization_id' => $pipeline->organization_id]);

        $moved = app(SalesPipelineService::class)->moveLeadToStage($lead, $stage);

        $this->assertTrue($moved->salesPipeline->is($pipeline));
        $this->assertTrue($moved->salesPipelineStage->is($stage));
        $this->assertNotNull($moved->stage_changed_at);
    }

    public function test_lead_cannot_move_to_other_organization_stage(): void
    {
        $organization = Organization::factory()->create();
        $lead = Lead::factory()->create(['organization_id' => $organization->id]);
        $otherStage = SalesPipelineStage::factory()->create();

        $this->expectException(ValidationException::class);

        app(SalesPipelineService::class)->moveLeadToStage($lead, $otherStage);
    }
}
