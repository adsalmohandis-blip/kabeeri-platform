<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Lead;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineStage;
use Illuminate\Validation\ValidationException;

class SalesPipelineService
{
    public function addStage(
        SalesPipeline $pipeline,
        string $name,
        string $slug,
        int $sortOrder = 0,
        int $probability = 0
    ): SalesPipelineStage {
        return $pipeline->stages()->create([
            'name' => $name,
            'slug' => $slug,
            'sort_order' => $sortOrder,
            'probability' => max(0, min(100, $probability)),
            'is_won' => false,
            'is_lost' => false,
            'status' => 'active',
        ]);
    }

    public function moveLeadToStage(Lead $lead, SalesPipelineStage $stage): Lead
    {
        $pipeline = $stage->pipeline;

        if ((int) $pipeline->organization_id !== (int) $lead->organization_id) {
            throw ValidationException::withMessages([
                'sales_pipeline_stage_id' => 'The selected stage does not belong to the lead organization.',
            ]);
        }

        $status = match (true) {
            $stage->is_won => 'converted',
            $stage->is_lost => 'lost',
            default => $lead->status,
        };

        $lead->forceFill([
            'sales_pipeline_id' => $pipeline->id,
            'sales_pipeline_stage_id' => $stage->id,
            'status' => $status,
            'stage_changed_at' => now(),
        ])->save();

        return $lead->refresh();
    }
}
