<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use Illuminate\Validation\ValidationException;

class WordPressImportPreviewService
{
    public function __construct(
        protected WordPressXmlParser $parser,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function preview(ImportJob $job): array
    {
        $path = $this->resolveXmlPath($job);
        $parsed = $this->parser->parseFile($path);

        $summary = [
            'pages' => $parsed['summary']['pages'] ?? 0,
            'posts' => $parsed['summary']['posts'] ?? 0,
            'authors' => $parsed['summary']['authors'] ?? 0,
            'categories' => $parsed['summary']['categories'] ?? 0,
            'tags' => $parsed['summary']['tags'] ?? 0,
            'attachments' => $parsed['summary']['attachments'] ?? 0,
            'unknown_post_types' => $parsed['unknown_post_types'] ?? [],
            'warnings' => $parsed['warnings'] ?? [],
            'site' => $parsed['site'] ?? ['title' => null, 'link' => null],
        ];

        $job->forceFill([
            'summary' => $summary,
            'status' => 'previewed',
        ])->save();

        return $job->refresh()->summary;
    }

    protected function resolveXmlPath(ImportJob $job): string
    {
        $metadata = is_array($job->metadata) ? $job->metadata : [];
        $candidates = array_filter([
            $metadata['file_path'] ?? null,
            $job->fileMedia?->path,
            $job->fileMedia?->relative_path,
        ]);

        foreach ($candidates as $candidate) {
            $path = (string) $candidate;
            $absolutePath = str_starts_with($path, base_path()) ? $path : base_path($path);

            if (is_file($absolutePath)) {
                return $absolutePath;
            }
        }

        throw ValidationException::withMessages([
            'file_media_id' => 'The import job does not reference a readable WordPress XML file.',
        ]);
    }
}
