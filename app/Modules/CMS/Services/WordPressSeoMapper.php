<?php

namespace App\Modules\CMS\Services;

use App\Models\ContentEntry;

class WordPressSeoMapper
{
    public function __construct(
        protected SeoService $seoService,
    ) {}

    /**
     * @param  array<string, mixed>  $meta
     * @return array{seo: array<string, mixed>, warnings: array<int, string>}
     */
    public function extract(array $meta): array
    {
        $seo = [
            'title' => $meta['_yoast_wpseo_title']
                ?? $meta['rank_math_title']
                ?? null,
            'description' => $meta['_yoast_wpseo_metadesc']
                ?? $meta['rank_math_description']
                ?? null,
            'canonical_url' => $meta['_yoast_wpseo_canonical']
                ?? $meta['rank_math_canonical_url']
                ?? null,
            'noindex' => $this->extractNoindex($meta),
            'og_title' => $meta['_yoast_wpseo_opengraph-title']
                ?? $meta['rank_math_facebook_title']
                ?? null,
            'og_description' => $meta['_yoast_wpseo_opengraph-description']
                ?? $meta['rank_math_facebook_description']
                ?? null,
            'og_image_media_id' => null,
        ];

        $unsupported = array_values(array_filter(array_keys($meta), static function (string $key): bool {
            return str_starts_with($key, '_yoast_wpseo_') || str_starts_with($key, 'rank_math_');
        }, ARRAY_FILTER_USE_BOTH));

        $supported = [
            '_yoast_wpseo_title',
            '_yoast_wpseo_metadesc',
            '_yoast_wpseo_canonical',
            '_yoast_wpseo_meta-robots-noindex',
            '_yoast_wpseo_opengraph-title',
            '_yoast_wpseo_opengraph-description',
            'rank_math_title',
            'rank_math_description',
            'rank_math_canonical_url',
            'rank_math_robots',
            'rank_math_facebook_title',
            'rank_math_facebook_description',
        ];

        $warnings = array_map(
            static fn (string $key): string => "Unsupported SEO metadata key [{$key}].",
            array_values(array_diff($unsupported, $supported)),
        );

        return ['seo' => $seo, 'warnings' => $warnings];
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array{entry: ContentEntry, warnings: array<int, string>, skipped: bool}
     */
    public function apply(ContentEntry $entry, array $meta, bool $overwrite = false): array
    {
        $extracted = $this->extract($meta);

        if (! $overwrite && $entry->seo_title !== null) {
            return [
                'entry' => $entry,
                'warnings' => array_merge($extracted['warnings'], ['Existing SEO was not overwritten.']),
                'skipped' => true,
            ];
        }

        return [
            'entry' => $this->seoService->write($entry, $extracted['seo']),
            'warnings' => $extracted['warnings'],
            'skipped' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function extractNoindex(array $meta): bool
    {
        if (($meta['_yoast_wpseo_meta-robots-noindex'] ?? null) === '1') {
            return true;
        }

        $robots = $meta['rank_math_robots'] ?? null;

        return is_string($robots) && str_contains($robots, 'noindex');
    }
}
