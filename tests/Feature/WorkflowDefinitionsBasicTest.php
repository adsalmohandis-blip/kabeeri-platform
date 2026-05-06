<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\WorkflowDefinitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WorkflowDefinitionsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_definition_can_be_created_and_activated(): void
    {
        $definition = app(WorkflowDefinitionService::class)->createForOrganization(Organization::factory()->create(), [
            'name' => 'Quote Approval',
            'slug' => 'quote-approval',
            'trigger_type' => 'quotation.issue',
            'steps' => [['type' => 'approval', 'label' => 'Manager approval']],
        ]);

        $active = app(WorkflowDefinitionService::class)->activate($definition);

        $this->assertNotNull($active->ulid);
        $this->assertSame('active', $active->status);
        $this->assertSame('approval', $active->steps[0]['type']);
    }

    public function test_workflow_definition_requires_valid_steps(): void
    {
        $this->expectException(ValidationException::class);

        app(WorkflowDefinitionService::class)->createForOrganization(Organization::factory()->create(), [
            'name' => 'Invalid Workflow',
            'slug' => 'invalid-workflow',
            'trigger_type' => 'manual',
            'steps' => [['type' => 'approval']],
        ]);
    }
}
