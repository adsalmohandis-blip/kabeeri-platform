<?php

namespace App\Modules\CMS\Services;

use App\Models\ContentEntry;
use App\Models\ImportJob;
use App\Models\Redirect;
use App\Models\RedirectSuggestion;

class RedirectSuggestionService
{
    public function __construct(
        protected RedirectService $redirectService,
    ) {}

    public function suggestForContent(ImportJob $job, string $sourceUrl, ContentEntry $entry, ?string $reason = null): RedirectSuggestion
    {
        return RedirectSuggestion::query()->create([
            'import_job_id' => $job->id,
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'source_url' => $this->normalizePath($sourceUrl),
            'target_url' => route('public.content-entry.show', [
                'username' => $entry->site->username,
                'contentEntry' => $entry,
            ], false),
            'status' => 'pending',
            'reason' => $reason ?? 'WordPress source URL maps to imported content.',
            'metadata' => [
                'content_entry_id' => $entry->id,
                'content_entry_slug' => $entry->slug,
            ],
        ]);
    }

    public function approve(RedirectSuggestion $suggestion): Redirect
    {
        $redirect = $this->redirectService->create([
            'organization_id' => $suggestion->organization_id,
            'site_id' => $suggestion->site_id,
            'source_path' => $suggestion->source_url,
            'target_url' => $suggestion->target_url,
            'status_code' => 301,
            'source_type' => $suggestion->getMorphClass(),
            'source_id' => $suggestion->id,
            'metadata' => ['approved_from_suggestion' => true],
        ]);

        $suggestion->forceFill(['status' => 'approved'])->save();

        return $redirect;
    }

    protected function normalizePath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: $url;

        return str_starts_with($path, '/') ? $path : '/'.$path;
    }
}
