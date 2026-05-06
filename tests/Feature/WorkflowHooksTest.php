<?php

namespace Tests\Feature;

use App\Models\BusinessTask;
use App\Models\WorkflowDefinition;
use App\Modules\BusinessOperations\Services\WorkflowHookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowHooksTest extends TestCase
{
    use RefreshDatabase;

    public function test_hook_dispatch_starts_matching_active_workflows(): void
    {
        $task = BusinessTask::factory()->create();
        WorkflowDefinition::factory()->create([
            'organization_id' => $task->organization_id,
            'trigger_type' => 'business_task.created',
            'status' => 'active',
        ]);
        WorkflowDefinition::factory()->create([
            'organization_id' => $task->organization_id,
            'trigger_type' => 'business_task.created',
            'status' => 'draft',
        ]);

        $runs = app(WorkflowHookService::class)->dispatch('business_task.created', $task, ['source' => 'test']);

        $this->assertCount(1, $runs);
        $this->assertSame('running', $runs->first()->status);
        $this->assertSame('business_task.created', $runs->first()->context['trigger_type']);
        $this->assertSame('test', $runs->first()->context['source']);
    }

    public function test_hook_dispatch_ignores_other_organizations(): void
    {
        $task = BusinessTask::factory()->create();
        WorkflowDefinition::factory()->create([
            'trigger_type' => 'business_task.created',
            'status' => 'active',
        ]);

        $runs = app(WorkflowHookService::class)->dispatch('business_task.created', $task);

        $this->assertCount(0, $runs);
    }
}
