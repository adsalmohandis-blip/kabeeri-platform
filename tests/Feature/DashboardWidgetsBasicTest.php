<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\ReportDefinition;
use App\Modules\BusinessOperations\Services\DashboardWidgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DashboardWidgetsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_widget_can_link_to_report_definition(): void
    {
        $organization = Organization::factory()->create();
        $report = ReportDefinition::factory()->create(['organization_id' => $organization->id]);

        $widget = app(DashboardWidgetService::class)->createForOrganization($organization, [
            'report_definition_id' => $report->id,
            'key' => 'invoice-total',
            'title' => 'Invoice Total',
            'widget_type' => 'metric',
        ]);

        $this->assertNotNull($widget->ulid);
        $this->assertTrue($widget->reportDefinition->is($report));
        $this->assertSame('active', $widget->status);
    }

    public function test_dashboard_widget_rejects_other_organization_report(): void
    {
        $this->expectException(ValidationException::class);

        app(DashboardWidgetService::class)->createForOrganization(Organization::factory()->create(), [
            'report_definition_id' => ReportDefinition::factory()->create()->id,
            'key' => 'bad-widget',
            'title' => 'Bad Widget',
        ]);
    }
}
