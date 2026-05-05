<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use App\Models\MediaAsset;
use App\Modules\Core\Services\MediaService;
use Illuminate\Support\Str;
use Throwable;

class WordPressMediaImportService
{
    public function __construct(
        protected MediaService $mediaService,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $attachments
     * @param  callable(array<string, mixed>): array<string, mixed>  $downloader
     * @return array{assets: array<int, MediaAsset>, warnings: array<int, array<string, mixed>>}
     */
    public function importAttachments(
        ImportJob $job,
        array $attachments,
        bool $download = false,
        ?callable $downloader = null,
    ): array {
        $assets = [];
        $warnings = [];

        foreach ($attachments as $attachment) {
            $url = $attachment['attachment_url'] ?? null;

            if (! is_string($url) || $url === '') {
                $warnings[] = ['attachment' => $attachment, 'message' => 'Attachment URL is missing.'];

                continue;
            }

            try {
                $assets[] = $this->createAssetFromAttachment($job, $attachment, $download, $downloader);
            } catch (Throwable $exception) {
                $warnings[] = [
                    'attachment_url' => $url,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return ['assets' => $assets, 'warnings' => $warnings];
    }

    /**
     * @param  array<string, mixed>  $attachment
     * @param  callable(array<string, mixed>): array<string, mixed>|null  $downloader
     */
    protected function createAssetFromAttachment(
        ImportJob $job,
        array $attachment,
        bool $download,
        ?callable $downloader,
    ): MediaAsset {
        $url = (string) $attachment['attachment_url'];
        $filename = basename(parse_url($url, PHP_URL_PATH) ?: 'wordpress-attachment');
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'bin';
        $downloadResult = null;

        if ($download) {
            if ($downloader === null) {
                throw new \RuntimeException('A downloader is required when media download is enabled.');
            }

            $downloadResult = $downloader($attachment);
        }

        $relativePath = $downloadResult['relative_path'] ?? 'wordpress-imports/'.$job->id.'/'.Str::slug(pathinfo($filename, PATHINFO_FILENAME)).'.'.$extension;

        return $this->mediaService->createAsset([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'company_id' => null,
            'uploaded_by' => $job->started_by,
            'disk' => $downloadResult['disk'] ?? 'external',
            'path' => $downloadResult['path'] ?? $relativePath,
            'relative_path' => $relativePath,
            'filename' => $filename,
            'original_filename' => $filename,
            'mime_type' => $downloadResult['mime_type'] ?? $this->guessMimeType($extension),
            'extension' => $extension,
            'size_bytes' => (int) ($downloadResult['size_bytes'] ?? 0),
            'visibility' => 'public',
            'metadata' => [
                'import_job_id' => $job->id,
                'wordpress_id' => $attachment['id'] ?? null,
                'external_url' => $url,
                'downloaded' => $downloadResult !== null,
            ],
        ]);
    }

    protected function guessMimeType(string $extension): string
    {
        return match (strtolower($extension)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
}
