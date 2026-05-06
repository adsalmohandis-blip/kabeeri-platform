<?php

namespace Tests\Feature;

use App\Models\BusinessProject;
use App\Models\EmployeeProfile;
use App\Modules\BusinessOperations\Services\BusinessProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProjectsAndTasksBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_have_task_assigned_to_employee(): void
    {
        $project = BusinessProject::factory()->create();
        $employee = EmployeeProfile::factory()->create(['organization_id' => $project->organization_id]);

        $task = app(BusinessProjectService::class)->addTask($project, [
            'assigned_employee_profile_id' => $employee->id,
            'title' => 'Prepare kickoff',
        ]);

        $this->assertNotNull($task->ulid);
        $this->assertTrue($task->project->is($project));
        $this->assertTrue($task->assignee->is($employee));
        $this->assertSame('todo', $task->status);
    }

    public function test_task_can_be_completed(): void
    {
        $task = app(BusinessProjectService::class)->addTask(BusinessProject::factory()->create(), [
            'title' => 'Prepare kickoff',
        ]);

        $completed = app(BusinessProjectService::class)->completeTask($task);

        $this->assertSame('done', $completed->status);
        $this->assertNotNull($completed->completed_at);
    }

    public function test_task_rejects_other_organization_assignee(): void
    {
        $this->expectException(ValidationException::class);

        app(BusinessProjectService::class)->addTask(BusinessProject::factory()->create(), [
            'assigned_employee_profile_id' => EmployeeProfile::factory()->create()->id,
            'title' => 'Wrong assignee',
        ]);
    }
}
