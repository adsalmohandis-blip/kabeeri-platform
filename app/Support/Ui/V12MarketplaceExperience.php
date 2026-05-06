<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class V12MarketplaceExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_marketplace', []);

        return [
            'config' => $config,
            'pages' => $config['pages'] ?? [],
            'lanes' => $config['marketplace_lanes'] ?? [],
            'taxonomy' => $config['taxonomy'] ?? [],
            'themes' => $config['themes'] ?? [],
            'theme_recipes' => $config['theme_recipes'] ?? [],
            'plugins' => $config['plugins'] ?? [],
            'bundles' => $config['bundles'] ?? [],
            'developer_onboarding' => $config['developer_onboarding'] ?? [],
            'lifecycle_statuses' => $config['lifecycle_statuses'] ?? [],
            'manifest_fields' => $config['manifest_fields'] ?? [],
            'permissions' => $config['permissions'] ?? [],
            'compatibility_axes' => $config['compatibility_axes'] ?? [],
            'qa_checklists' => $config['qa_checklists'] ?? [],
            'component_library' => $config['component_library'] ?? [],
            'review_flow' => $config['review_flow'] ?? [],
            'licensing' => $config['licensing'] ?? [],
            'inventory' => self::liveInventory(),
            'release' => self::releaseReadiness(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function pageData(string $page, ?string $slug = null): array
    {
        $all = self::all();
        $selectedTheme = $slug
            ? collect($all['themes'])->firstWhere('slug', $slug)
            : collect($all['themes'])->first();
        $selectedPlugin = $slug
            ? collect($all['plugins'])->firstWhere('slug', $slug)
            : collect($all['plugins'])->first();

        return [
            ...$all,
            'page' => $page,
            'slug' => $slug,
            'page_config' => $all['pages'][$page] ?? $all['pages']['marketplace_home'],
            'selected_theme' => $selectedTheme ?: collect($all['themes'])->first(),
            'selected_plugin' => $selectedPlugin ?: collect($all['plugins'])->first(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $config = config('kabeeri_marketplace', []);
        $pageRoutes = array_column($config['pages'] ?? [], 'route');
        $adminRoutes = $config['admin_routes'] ?? [];
        $databaseTables = $config['database_tables'] ?? [];

        return [
            'version' => $config['version'] ?? null,
            'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
            'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
            'v11_ready' => V11PublicExperience::isReleaseCandidateReady(),
            'page_count' => count($config['pages'] ?? []),
            'theme_count' => count($config['themes'] ?? []),
            'plugin_count' => count($config['plugins'] ?? []),
            'bundle_count' => count($config['bundles'] ?? []),
            'lifecycle_status_count' => count($config['lifecycle_statuses'] ?? []),
            'qa_theme_count' => count($config['qa_checklists']['theme'] ?? []),
            'qa_plugin_count' => count($config['qa_checklists']['plugin'] ?? []),
            'component_count' => count($config['component_library']['components'] ?? []),
            'missing_routes' => self::missingRoutes($pageRoutes),
            'missing_admin_routes' => self::missingRoutes($adminRoutes),
            'missing_database_tables' => array_values(array_filter(
                $databaseTables,
                fn (string $table): bool => ! self::hasTable($table),
            )),
            'task_tracker_synced' => self::v12TaskTrackerSynced(),
            'docs' => [
                'marketplace_ux' => file_exists(base_path('docs/kabeeri/ui/V12_MARKETPLACE_DEVELOPER_UX.md')),
                'release_report' => file_exists(base_path('docs/kabeeri/ui/V12_RELEASE_CANDIDATE.md')),
            ],
            'smoke_tests' => file_exists(base_path('tests/Feature/V12MarketplaceExperienceTest.php')),
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V12'
            && $report['v9_ready']
            && $report['v10_ready']
            && $report['v11_ready']
            && $report['page_count'] >= 19
            && $report['theme_count'] >= 3
            && $report['plugin_count'] >= 3
            && $report['bundle_count'] >= 3
            && $report['lifecycle_status_count'] >= 9
            && $report['qa_theme_count'] >= 10
            && $report['qa_plugin_count'] >= 12
            && $report['component_count'] >= 10
            && $report['missing_routes'] === []
            && $report['missing_admin_routes'] === []
            && $report['missing_database_tables'] === []
            && $report['task_tracker_synced']
            && $report['smoke_tests']
            && ! in_array(false, $report['docs'], true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function liveInventory(): array
    {
        $tables = array_map(
            fn (string $table): array => [
                'name' => $table,
                'exists' => self::hasTable($table),
                'count' => self::safeCount($table),
            ],
            config('kabeeri_marketplace.database_tables', []),
        );

        return [
            'tables' => $tables,
            'routes' => array_map(
                fn (array $page): array => [
                    'label' => $page['label'],
                    'route' => $page['route'],
                    'exists' => Route::has($page['route']),
                ],
                config('kabeeri_marketplace.pages', []),
            ),
            'admin_routes' => array_map(
                fn (string $route): array => [
                    'route' => $route,
                    'exists' => Route::has($route),
                ],
                config('kabeeri_marketplace.admin_routes', []),
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function releaseReadiness(): array
    {
        $gates = array_map(function (array $gate): array {
            $ready = match ($gate['key']) {
                'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
                'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
                'v11_ready' => V11PublicExperience::isReleaseCandidateReady(),
                'routes_ready' => self::missingRoutes(array_column(config('kabeeri_marketplace.pages', []), 'route')) === [],
                'admin_routes_ready' => self::missingRoutes(config('kabeeri_marketplace.admin_routes', [])) === [],
                'database_ready' => self::databaseTablesReady(),
                'docs_ready' => file_exists(base_path('docs/kabeeri/ui/V12_MARKETPLACE_DEVELOPER_UX.md')),
                'smoke_tests_ready' => file_exists(base_path('tests/Feature/V12MarketplaceExperienceTest.php')),
                'tracker_synced' => self::v12TaskTrackerSynced(),
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, config('kabeeri_marketplace.release_gates', []));

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V12MarketplaceExperienceTest',
                'php artisan test --filter=V11PublicExperienceTest',
                'php artisan test --filter=V10AdminExperienceTest',
                'vendor/bin/pint --test',
                'npm run build',
            ],
        ];
    }

    /**
     * @param  list<string>  $routes
     * @return list<string>
     */
    private static function missingRoutes(array $routes): array
    {
        return array_values(array_filter(
            $routes,
            fn (string $route): bool => ! Route::has($route),
        ));
    }

    private static function databaseTablesReady(): bool
    {
        return collect(config('kabeeri_marketplace.database_tables', []))
            ->every(fn (string $table): bool => self::hasTable($table));
    }

    private static function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    private static function safeCount(string $table): int
    {
        try {
            return self::hasTable($table) ? DB::table($table)->count() : 0;
        } catch (Throwable) {
            return 0;
        }
    }

    private static function v12TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v12.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 31 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }
}
