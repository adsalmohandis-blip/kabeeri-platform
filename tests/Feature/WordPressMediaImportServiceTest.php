<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Modules\CMS\Services\WordPressMediaImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class WordPressMediaImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_attachments_can_be_imported_as_metadata_first_media_assets(): void
    {
        $job = ImportJob::factory()->create();

        $result = app(WordPressMediaImportService::class)->importAttachments($job, [[
            'id' => '300',
            'attachment_url' => 'https://legacy.example.test/uploads/hero.jpg',
        ]]);

        $this->assertCount(1, $result['assets']);
        $this->assertSame([], $result['warnings']);
        $asset = $result['assets'][0];
        $this->assertSame('external', $asset->disk);
        $this->assertSame('hero.jpg', $asset->filename);
        $this->assertSame('image/jpeg', $asset->mime_type);
        $this->assertSame('https://legacy.example.test/uploads/hero.jpg', $asset->metadata['external_url']);
        $this->assertFalse($asset->metadata['downloaded']);
    }

    public function test_optional_download_uses_injected_downloader(): void
    {
        $job = ImportJob::factory()->create();

        $result = app(WordPressMediaImportService::class)->importAttachments($job, [[
            'id' => '301',
            'attachment_url' => 'https://legacy.example.test/uploads/logo.png',
        ]], download: true, downloader: fn (): array => [
            'disk' => 'public',
            'path' => 'organizations/'.$job->organization_id.'/imports/logo.png',
            'relative_path' => 'imports/logo.png',
            'mime_type' => 'image/png',
            'size_bytes' => 2048,
        ]);

        $asset = $result['assets'][0];
        $this->assertSame('public', $asset->disk);
        $this->assertSame(2048, $asset->size_bytes);
        $this->assertTrue($asset->metadata['downloaded']);
    }

    public function test_failed_download_becomes_warning_without_crashing_import(): void
    {
        $job = ImportJob::factory()->create();

        $result = app(WordPressMediaImportService::class)->importAttachments($job, [[
            'id' => '302',
            'attachment_url' => 'https://legacy.example.test/uploads/missing.png',
        ]], download: true, downloader: fn () => throw new RuntimeException('Download failed'));

        $this->assertSame([], $result['assets']);
        $this->assertSame('Download failed', $result['warnings'][0]['message']);
    }
}
