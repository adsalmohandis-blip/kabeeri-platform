<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Modules\CMS\Services\WordPressImportPreviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WordPressImportPreviewServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_parses_xml_and_saves_summary_on_import_job(): void
    {
        $job = ImportJob::factory()->create([
            'source_type' => 'wordpress',
            'status' => 'draft',
            'metadata' => [
                'file_path' => 'tests/Fixtures/wordpress-small.xml',
            ],
        ]);

        $summary = app(WordPressImportPreviewService::class)->preview($job);

        $this->assertSame(1, $summary['pages']);
        $this->assertSame(1, $summary['posts']);
        $this->assertSame(1, $summary['authors']);
        $this->assertSame(1, $summary['categories']);
        $this->assertSame(1, $summary['tags']);
        $this->assertSame(1, $summary['attachments']);
        $this->assertSame(['portfolio' => 1], $summary['unknown_post_types']);
        $this->assertSame('Legacy Site', $summary['site']['title']);
        $this->assertSame('previewed', $job->refresh()->status);
        $this->assertSame($summary, $job->summary);
    }

    public function test_preview_requires_readable_xml_reference(): void
    {
        $job = ImportJob::factory()->create([
            'metadata' => ['file_path' => 'missing.xml'],
        ]);

        $this->expectException(ValidationException::class);

        app(WordPressImportPreviewService::class)->preview($job);
    }
}
