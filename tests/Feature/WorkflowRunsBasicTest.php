<?php

namespace Tests\Feature;

use App\Models\BusinessTask;
use App\Models\WorkflowDefinition;
use App\Modules\BusinessOperations\Services\WorkflowRunService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WorkflowRunsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_workflow_definition_can_start_run(): void
    {
        $definition = WorkflowDefinition::factory()->create(['status' => 'active']);
        $task = BusinessTask::factory()->create(['organization_id' => $definition->organization_id]);

        $run = app(WorkflowRunService::class)->start($definition, $task, ['reason' => 'manual']);

        $this->assertNotNull($run->ulid);
        $this->assertSame('running', $run->status);
        $this->assertSame($task::class, $run->subject_type);
        $this->assertSame(['reason' => 'manual'], $run->context);
    }

    public function test_draft_workflow_definition_cannot_start_run(): void
    {
        $this->expectException(ValidationException::class);

        app(WorkflowRunService::class)->start(
            WorkflowDefinition::factory()->create(['status' => 'draft']),
            BusinessTask::factory()->create()
        );
    }

    public function test_workflow_run_can_complete(): void
    {
        $definition = WorkflowDefinition::factory()->create(['status' => 'active']);
        $run = app(WorkflowRunService::class)->start($definition, BusinessTask::factory()->create([
            'organization_id' => $definition->organization_id,
        ]));

        $completed = app(WorkflowRunService::class)->complete($run);

        $this->assertSame('completed', $completed->status);
        $this->assertNotNull($completed->completed_at);
    }
}
