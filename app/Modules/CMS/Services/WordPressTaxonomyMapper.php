<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportJob;
use App\Models\MigrationMapping;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;

class WordPressTaxonomyMapper
{
    /**
     * @param  array<int, array<string, mixed>>  $terms
     * @return array{terms: array<int, TaxonomyTerm>, warnings: array<int, array<string, mixed>>}
     */
    public function mapTerms(ImportJob $job, array $terms): array
    {
        $mapped = [];
        $warnings = [];

        foreach ($terms as $term) {
            $domain = (string) ($term['domain'] ?? $term['taxonomy'] ?? '');
            $taxonomySlug = match ($domain) {
                'category' => 'category',
                'post_tag' => 'tag',
                default => null,
            };

            if ($taxonomySlug === null) {
                $warnings[] = [
                    'source' => 'wordpress_taxonomy',
                    'domain' => $domain,
                    'slug' => $term['slug'] ?? null,
                    'message' => 'Unsupported WordPress taxonomy.',
                ];

                continue;
            }

            $taxonomy = Taxonomy::query()->firstOrCreate(
                [
                    'organization_id' => $job->organization_id,
                    'site_id' => $job->site_id,
                    'slug' => $taxonomySlug,
                ],
                [
                    'name' => $taxonomySlug === 'category' ? 'Categories' : 'Tags',
                    'type' => $taxonomySlug,
                ],
            );

            $mappedTerm = TaxonomyTerm::query()->firstOrCreate(
                [
                    'taxonomy_id' => $taxonomy->id,
                    'slug' => (string) $term['slug'],
                ],
                [
                    'name' => (string) ($term['name'] ?? $term['slug']),
                    'description' => $term['description'] ?? null,
                ],
            );

            MigrationMapping::query()->updateOrCreate(
                [
                    'import_job_id' => $job->id,
                    'source_type' => 'wordpress_'.$taxonomySlug,
                    'source_id' => isset($term['id']) ? (string) $term['id'] : null,
                    'source_key' => (string) $term['slug'],
                ],
                [
                    'target_type' => $mappedTerm->getMorphClass(),
                    'target_id' => $mappedTerm->id,
                    'mapping_status' => 'mapped',
                    'mapping_strategy' => 'slug_match_or_create',
                    'metadata' => ['term' => $term],
                ],
            );

            $mapped[] = $mappedTerm;
        }

        return ['terms' => $mapped, 'warnings' => $warnings];
    }
}
