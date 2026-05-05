<?php

namespace App\Modules\Core\Services;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Site;
use App\Models\ThemeAppRecipe;

class DemoImportService
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  array{overwrite?: bool, pages?: array<int, array<string, string>>}  $options
     * @return array{created: array<int, ContentEntry>, skipped: array<int, string>}
     */
    public function importForSite(ThemeAppRecipe $recipe, Site $site, array $options = []): array
    {
        $overwrite = (bool) ($options['overwrite'] ?? false);
        $pages = $options['pages'] ?? $this->defaultPages($recipe);
        $contentType = $this->pageContentType($site);
        $created = [];
        $skipped = [];

        foreach ($pages as $page) {
            $existing = ContentEntry::query()
                ->where('site_id', $site->id)
                ->where('slug', $page['slug'])
                ->first();

            if ($existing && ! $overwrite) {
                $skipped[] = $page['slug'];

                continue;
            }

            $entry = ContentEntry::query()->updateOrCreate(
                [
                    'site_id' => $site->id,
                    'slug' => $page['slug'],
                ],
                [
                    'organization_id' => $site->organization_id,
                    'content_type_id' => $contentType->id,
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'] ?? null,
                    'body' => $page['body'] ?? null,
                    'status' => 'draft',
                    'visibility' => 'public',
                    'metadata' => [
                        'demo_import' => true,
                        'theme_app_recipe_id' => $recipe->id,
                    ],
                ],
            );

            $this->activityLogger->log(
                action: 'theme_demo.imported_page',
                organizationId: $site->organization_id,
                siteId: $site->id,
                description: 'Theme demo page imported',
                subject: $entry,
            );

            $created[] = $entry->refresh();
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    protected function pageContentType(Site $site): ContentType
    {
        return ContentType::query()->firstOrCreate(
            [
                'organization_id' => $site->organization_id,
                'site_id' => $site->id,
                'slug' => 'page',
            ],
            [
                'name' => 'Page',
                'description' => 'CMS page content.',
                'fields' => [],
                'status' => 'active',
            ],
        );
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function defaultPages(ThemeAppRecipe $recipe): array
    {
        return [
            [
                'title' => 'Home',
                'slug' => 'home',
                'excerpt' => 'Demo home page.',
                'body' => '<p>Welcome to your demo '.$recipe->app_type.' app.</p>',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'excerpt' => 'Demo contact page.',
                'body' => '<p>Contact us through this demo page.</p>',
            ],
        ];
    }
}
