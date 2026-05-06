<?php

namespace Tests\Feature;

use App\Filament\Resources\ApprovalRequests\ApprovalRequestResource;
use App\Filament\Resources\BusinessProjects\BusinessProjectResource;
use App\Filament\Resources\DashboardWidgets\DashboardWidgetResource;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Resources\EmployeeProfiles\EmployeeProfileResource;
use App\Filament\Resources\ReportDefinitions\ReportDefinitionResource;
use App\Filament\Resources\WorkflowDefinitions\WorkflowDefinitionResource;
use App\Models\ApprovalRequest;
use App\Models\BusinessProject;
use App\Models\DashboardWidget;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\ReportDefinition;
use App\Models\User;
use App\Models\WorkflowDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPeopleWorkflowReportsResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_people_workflow_report_indexes(): void
    {
        $owner = User::factory()->create();
        Organization::factory()->create(['owner_user_id' => $owner->id]);

        $this->actingAs($owner);

        $this->get(route('filament.admin.resources.employee-profiles.index'))->assertOk();
        $this->get(route('filament.admin.resources.departments.index'))->assertOk();
        $this->get(route('filament.admin.resources.business-projects.index'))->assertOk();
        $this->get(route('filament.admin.resources.workflow-definitions.index'))->assertOk();
        $this->get(route('filament.admin.resources.approval-requests.index'))->assertOk();
        $this->get(route('filament.admin.resources.report-definitions.index'))->assertOk();
        $this->get(route('filament.admin.resources.dashboard-widgets.index'))->assertOk();
    }

    public function test_people_workflow_report_resource_queries_are_tenant_scoped(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);

        $visibleEmployee = EmployeeProfile::factory()->create(['organization_id' => $organization->id]);
        $hiddenEmployee = EmployeeProfile::factory()->create();
        $visibleDepartment = Department::factory()->create(['organization_id' => $organization->id]);
        $hiddenDepartment = Department::factory()->create();
        $visibleProject = BusinessProject::factory()->create(['organization_id' => $organization->id]);
        $hiddenProject = BusinessProject::factory()->create();
        $visibleWorkflow = WorkflowDefinition::factory()->create(['organization_id' => $organization->id]);
        $hiddenWorkflow = WorkflowDefinition::factory()->create();
        $visibleApproval = ApprovalRequest::factory()->create(['organization_id' => $organization->id]);
        $hiddenApproval = ApprovalRequest::factory()->create();
        $visibleReport = ReportDefinition::factory()->create(['organization_id' => $organization->id]);
        $hiddenReport = ReportDefinition::factory()->create();
        $visibleWidget = DashboardWidget::factory()->create(['organization_id' => $organization->id]);
        $hiddenWidget = DashboardWidget::factory()->create();

        $this->actingAs($owner);

        $this->assertTrue(EmployeeProfileResource::getEloquentQuery()->whereKey($visibleEmployee->id)->exists());
        $this->assertFalse(EmployeeProfileResource::getEloquentQuery()->whereKey($hiddenEmployee->id)->exists());
        $this->assertTrue(DepartmentResource::getEloquentQuery()->whereKey($visibleDepartment->id)->exists());
        $this->assertFalse(DepartmentResource::getEloquentQuery()->whereKey($hiddenDepartment->id)->exists());
        $this->assertTrue(BusinessProjectResource::getEloquentQuery()->whereKey($visibleProject->id)->exists());
        $this->assertFalse(BusinessProjectResource::getEloquentQuery()->whereKey($hiddenProject->id)->exists());
        $this->assertTrue(WorkflowDefinitionResource::getEloquentQuery()->whereKey($visibleWorkflow->id)->exists());
        $this->assertFalse(WorkflowDefinitionResource::getEloquentQuery()->whereKey($hiddenWorkflow->id)->exists());
        $this->assertTrue(ApprovalRequestResource::getEloquentQuery()->whereKey($visibleApproval->id)->exists());
        $this->assertFalse(ApprovalRequestResource::getEloquentQuery()->whereKey($hiddenApproval->id)->exists());
        $this->assertTrue(ReportDefinitionResource::getEloquentQuery()->whereKey($visibleReport->id)->exists());
        $this->assertFalse(ReportDefinitionResource::getEloquentQuery()->whereKey($hiddenReport->id)->exists());
        $this->assertTrue(DashboardWidgetResource::getEloquentQuery()->whereKey($visibleWidget->id)->exists());
        $this->assertFalse(DashboardWidgetResource::getEloquentQuery()->whereKey($hiddenWidget->id)->exists());
    }
}
