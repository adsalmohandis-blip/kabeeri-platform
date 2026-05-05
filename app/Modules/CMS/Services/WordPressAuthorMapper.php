<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use App\Models\MigrationMapping;
use App\Models\User;

class WordPressAuthorMapper
{
    /**
     * @param  array<string, mixed>  $author
     */
    public function map(ImportJob $job, array $author, ?User $fallbackUser = null): MigrationMapping
    {
        $sourceId = isset($author['id']) ? (string) $author['id'] : null;
        $sourceKey = $author['login'] ?? $author['email'] ?? $author['display_name'] ?? $sourceId;

        $attributes = [
            'target_type' => $fallbackUser?->getMorphClass(),
            'target_id' => $fallbackUser?->id,
            'mapping_status' => $fallbackUser === null ? 'metadata_only' : 'mapped',
            'mapping_strategy' => $fallbackUser === null ? 'store_author_metadata' : 'fallback_user',
            'metadata' => [
                'author' => $author,
                'auto_created_user' => false,
            ],
        ];

        return MigrationMapping::query()->updateOrCreate(
            [
                'import_job_id' => $job->id,
                'source_type' => 'wordpress_author',
                'source_id' => $sourceId,
                'source_key' => $sourceKey,
            ],
            $attributes,
        );
    }
}
