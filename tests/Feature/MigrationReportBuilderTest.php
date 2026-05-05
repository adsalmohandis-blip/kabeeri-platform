<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Models\RedirectSuggestion;
use App\Modules\CMS\Services\MigrationReportBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MigrationReportBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_is_generated_from_import_job_data(): void
    {
        $job = ImportJob::factory()->create([
            'summary' => ['unknown_post_types' => ['portfolio' => 2]],
        ]);
        ImportRecord::factory()->create([
            'import_job_id' => $job->id,
            'source_entity_type' => 'post',
            'status' => 'imported',
            'warnings' => [['type' => 'shortcode', 'name' => 'mystery']],
            'metadata' => ['seo_mapped' => true],
        ]);
        ImportRecord::factory()->create([
            'import_job_id' => $job->id,
            'source_entity_type' => 'attachment',
            'status' => 'failed',
            'errors' => ['download_failed'],
        ]);
        RedirectSuggestion::factory()->create(['import_job_id' => $job->id]);

        $report = app(MigrationReportBuilder::class)->build($job);

        $this->assertSame($job->id, $report->import_job_id);
        $this->assertSame(1, $report->data['imported_pages_posts']);
        $this->assertSame(1, $report->data['failed_media']);
        $this->assertSame(1, $report->data['seo_mapping_count']);
        $this->assertSame(1, $report->data['redirect_suggestions']);
        $this->assertSame(['portfolio' => 2], $report->data['unsupported_post_types']);
        $this->assertSame('mystery', $report->data['shortcode_warnings'][0]['name']);
        $this->assertContains('Fix failed records and rerun the affected import step.', $report->data['next_recommended_actions']);
        $this->assertTrue($job->reports()->whereKey($report->id)->exists());
    }
}
