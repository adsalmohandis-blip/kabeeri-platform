<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Modules\CMS\Services\WordPressRollbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressRollbackServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_imported_content_can_be_soft_deleted_and_marked_rolled_back(): void
    {
        $job = ImportJob::factory()->create();
        $contentType = ContentType::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
        ]);
        $entry = ContentEntry::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'content_type_id' => $contentType->id,
        ]);
        $record = ImportRecord::factory()->create([
            'import_job_id' => $job->id,
            'target_type' => $entry->getMorphClass(),
            'target_id' => $entry->id,
            'status' => 'imported',
        ]);
        $entry->forceFill(['updated_at' => $record->created_at])->save();

        $result = app(WordPressRollbackService::class)->rollback($job);

        $this->assertSame(1, $result['rolled_back']);
        $this->assertSoftDeleted('content_entries', ['id' => $entry->id]);
        $this->assertSame('rolled_back', $record->refresh()->status);
        $this->assertSame('rolled_back', $job->refresh()->status);
    }

    public function test_edited_imported_content_is_not_rolled_back(): void
    {
        $job = ImportJob::factory()->create();
        $contentType = ContentType::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
        ]);
        $entry = ContentEntry::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'content_type_id' => $contentType->id,
        ]);
        $record = ImportRecord::factory()->create([
            'import_job_id' => $job->id,
            'target_type' => $entry->getMorphClass(),
            'target_id' => $entry->id,
            'status' => 'imported',
        ]);
        $entry->forceFill(['updated_at' => $record->created_at->copy()->addMinute()])->save();

        $result = app(WordPressRollbackService::class)->rollback($job);

        $this->assertSame(0, $result['rolled_back']);
        $this->assertSame('Target was edited after import.', $result['skipped'][0]['reason']);
        $this->assertFalse($entry->refresh()->trashed());
        $this->assertSame('imported', $record->refresh()->status);
    }
}
