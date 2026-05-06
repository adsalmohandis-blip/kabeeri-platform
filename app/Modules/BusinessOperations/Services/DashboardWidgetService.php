<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\DashboardWidget;
use App\Models\Organization;
use App\Models\ReportDefinition;
use Illuminate\Validation\ValidationException;

class DashboardWidgetService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): DashboardWidget
    {
        if (($attributes['report_definition_id'] ?? null) !== null) {
            $report = ReportDefinition::query()->findOrFail($attributes['report_definition_id']);

            if ((int) $report->organization_id !== (int) $organization->id) {
                throw ValidationException::withMessages([
                    'report_definition_id' => 'Report definition must belong to this organization.',
                ]);
            }
        }

        return DashboardWidget::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
            'widget_type' => $attributes['widget_type'] ?? 'metric',
        ]);
    }
}
