<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Organization;
use App\Models\ReportDefinition;
use App\Models\ReportSnapshot;

class ReportService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDefinition(Organization $organization, array $attributes): ReportDefinition
    {
        return ReportDefinition::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @param  array<string, mixed>  $data
     */
    public function snapshot(ReportDefinition $definition, array $parameters, array $data): ReportSnapshot
    {
        return ReportSnapshot::query()->create([
            'organization_id' => $definition->organization_id,
            'report_definition_id' => $definition->id,
            'status' => 'ready',
            'parameters' => $parameters,
            'data' => $data,
            'generated_at' => now(),
        ]);
    }
}
