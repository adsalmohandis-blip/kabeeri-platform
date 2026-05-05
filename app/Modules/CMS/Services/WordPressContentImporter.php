<?php

namespace App\Modules\CMS\Services;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Models\MigrationMapping;
use App\Models\TaxonomyTerm;
use App\Models\User;

class WordPressContentImporter
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, ContentEntry>
     */
    public function import(ImportJob $job, array $items): array
    {
        $entries = [];

        foreach ($items as $item) {
            $entries[] = $this->importItem($job, $item);
        }

        return $entries;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function importItem(ImportJob $job, array $item): ContentEntry
    {
        $sourceType = (string) ($item['type'] ?? 'post');
        $contentType = $this->contentTypeFor($job, $sourceType);
        $status = $this->mapStatus((string) ($item['status'] ?? 'draft'), $job);
        $authorId = $this->resolveAuthorId($job, $item['author'] ?? null);

        $entry = ContentEntry::query()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'content_type_id' => $contentType->id,
            'author_user_id' => $authorId,
            'title' => (string) ($item['title'] ?? 'Untitled'),
            'slug' => (string) ($item['slug'] ?? 'imported-'.$item['id']),
            'excerpt' => $item['excerpt'] ?? null,
            'body' => $item['content_html'] ?? null,
            'status' => $status,
            'visibility' => $status === 'private' ? 'private' : 'public',
            'published_at' => $status === 'published' ? ($item['date'] ?? now()) : null,
            'metadata' => [
                'import_job_id' => $job->id,
                'wordpress_id' => $item['id'] ?? null,
                'wordpress_status' => $item['status'] ?? null,
                'wordpress_type' => $sourceType,
            ],
        ]);

        $this->attachMappedTerms($job, $entry, $item['terms'] ?? []);

        ImportRecord::query()->create([
            'import_job_id' => $job->id,
            'source_entity_type' => $sourceType,
            'source_entity_id' => isset($item['id']) ? (string) $item['id'] : null,
            'target_type' => $entry->getMorphClass(),
            'target_id' => $entry->id,
            'status' => 'imported',
            'warnings' => $status !== 'published' && ($item['status'] ?? null) === 'publish'
                ? ['publish_disabled_imported_as_draft']
                : null,
            'errors' => null,
            'source_payload' => $item,
            'metadata' => ['auto_published' => $status === 'published'],
        ]);

        return $entry->refresh();
    }

    protected function contentTypeFor(ImportJob $job, string $sourceType): ContentType
    {
        $slug = $sourceType === 'page' ? 'page' : 'post';

        return ContentType::query()->firstOrCreate(
            [
                'organization_id' => $job->organization_id,
                'site_id' => $job->site_id,
                'slug' => $slug,
            ],
            [
                'name' => ucfirst($slug),
                'description' => 'Imported WordPress '.$slug.' content.',
                'fields' => [],
                'status' => 'active',
            ],
        );
    }

    protected function mapStatus(string $wordpressStatus, ImportJob $job): string
    {
        $settings = is_array($job->settings) ? $job->settings : [];
        $allowPublish = (bool) ($settings['allow_publish'] ?? false);

        return match ($wordpressStatus) {
            'publish' => $allowPublish ? 'published' : 'draft',
            'private' => 'private',
            default => 'draft',
        };
    }

    protected function resolveAuthorId(ImportJob $job, mixed $author): ?int
    {
        if ($author === null || $author === '') {
            return null;
        }

        $mapping = MigrationMapping::query()
            ->where('import_job_id', $job->id)
            ->where('source_type', 'wordpress_author')
            ->where('source_key', (string) $author)
            ->where('target_type', (new User)->getMorphClass())
            ->first();

        return $mapping?->target_id;
    }

    /**
     * @param  array<int, array<string, mixed>>  $terms
     */
    protected function attachMappedTerms(ImportJob $job, ContentEntry $entry, array $terms): void
    {
        $termIds = [];

        foreach ($terms as $term) {
            $domain = (string) ($term['domain'] ?? '');
            $sourceType = match ($domain) {
                'category' => 'wordpress_category',
                'post_tag' => 'wordpress_tag',
                default => null,
            };

            if ($sourceType === null) {
                continue;
            }

            $mapping = MigrationMapping::query()
                ->where('import_job_id', $job->id)
                ->where('source_type', $sourceType)
                ->where('source_key', (string) ($term['slug'] ?? ''))
                ->where('target_type', (new TaxonomyTerm)->getMorphClass())
                ->first();

            if ($mapping?->target_id) {
                $termIds[] = $mapping->target_id;
            }
        }

        if ($termIds !== []) {
            $entry->taxonomyTerms()->syncWithoutDetaching(array_unique($termIds));
        }
    }
}
