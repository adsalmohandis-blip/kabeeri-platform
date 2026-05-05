<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressImportJobsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_job_can_be_created_and_scoped(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $job = ImportJob::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_type' => 'wordpress',
            'source_name' => 'legacy.example.test',
        ]);

        $this->assertSame('draft', $job->status);
        $this->assertSame('wordpress', $job->source_type);
        $this->assertTrue($organization->importJobs()->whereKey($job->id)->exists());
        $this->assertTrue($site->importJobs()->whereKey($job->id)->exists());
    }

    public function test_import_batches_and_records_can_be_tracked(): void
    {
        $job = ImportJob::factory()->create();

        $batch = ImportBatch::factory()->create([
            'import_job_id' => $job->id,
            'batch_type' => 'pages',
            'total_records' => 2,
        ]);
        $record = ImportRecord::factory()->create([
            'import_job_id' => $job->id,
            'source_entity_type' => 'page',
            'source_entity_id' => '42',
            'status' => 'pending',
            'warnings' => ['shortcode_detected'],
            'source_payload' => ['title' => 'Legacy Page'],
        ]);

        $this->assertTrue($job->batches()->whereKey($batch->id)->exists());
        $this->assertTrue($job->records()->whereKey($record->id)->exists());
        $this->assertSame(['shortcode_detected'], $record->warnings);
        $this->assertSame(['title' => 'Legacy Page'], $record->source_payload);
    }
}
