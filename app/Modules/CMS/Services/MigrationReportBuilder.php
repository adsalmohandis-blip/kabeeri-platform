<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Models\MigrationReport;

class MigrationReportBuilder
{
    public function build(ImportJob $job): MigrationReport
    {
        $records = $job->records()->get();
        $warnings = $records->flatMap(fn (ImportRecord $record): array => is_array($record->warnings) ? $record->warnings : [])->values()->all();
        $errors = $records->flatMap(fn (ImportRecord $record): array => is_array($record->errors) ? $record->errors : [])->values()->all();

        $data = [
            'import_job_summary' => $job->summary ?? [],
            'imported_pages_posts' => $records
                ->whereIn('source_entity_type', ['page', 'post'])
                ->where('status', 'imported')
                ->count(),
            'imported_media_count' => $records
                ->where('source_entity_type', 'attachment')
                ->where('status', 'imported')
                ->count(),
            'failed_media' => $records
                ->where('source_entity_type', 'attachment')
                ->where('status', 'failed')
                ->count(),
            'seo_mapping_count' => $records
                ->filter(fn (ImportRecord $record): bool => (bool) data_get($record->metadata, 'seo_mapped'))
                ->count(),
            'redirect_suggestions' => $job->redirectSuggestions()->count(),
            'shortcode_warnings' => array_values(array_filter(
                $warnings,
                static fn (mixed $warning): bool => is_array($warning) && ($warning['type'] ?? null) === 'shortcode',
            )),
            'unsupported_post_types' => $job->summary['unknown_post_types'] ?? [],
            'warnings' => $warnings,
            'errors' => $errors,
            'next_recommended_actions' => $this->nextActions($warnings, $errors),
        ];

        return MigrationReport::query()->create([
            'import_job_id' => $job->id,
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'status' => 'generated',
            'data' => $data,
        ]);
    }

    /**
     * @param  array<int, mixed>  $warnings
     * @param  array<int, mixed>  $errors
     * @return array<int, string>
     */
    protected function nextActions(array $warnings, array $errors): array
    {
        $actions = ['Review imported drafts before publishing.'];

        if ($warnings !== []) {
            $actions[] = 'Review migration warnings and decide what should be fixed manually.';
        }

        if ($errors !== []) {
            $actions[] = 'Fix failed records and rerun the affected import step.';
        }

        return $actions;
    }
}
