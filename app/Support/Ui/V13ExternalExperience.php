<?php

namespace App\Support\Ui;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class V13ExternalExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_external', []);

        return [
            'config' => $config,
            'pages' => $config['pages'] ?? [],
            'mall_sections' => self::mallSections(),
            'search_filters' => $config['search_filters'] ?? [],
            'trust_badges' => $config['trust_badges'] ?? [],
            'claim_report_flow' => $config['claim_report_flow'] ?? [],
            'customer_steps' => $config['customer_steps'] ?? [],
            'partner_paths' => $config['partner_paths'] ?? [],
            'referral_metrics' => $config['referral_metrics'] ?? [],
            'campaign_resources' => $config['campaign_resources'] ?? [],
            'network' => $config['network'] ?? [],
            'legal_verification_steps' => $config['legal_verification_steps'] ?? [],
            'inventory' => self::liveInventory(),
            'release' => self::releaseReadiness(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function pageData(string $page): array
    {
        $all = self::all();

        return [
            ...$all,
            'page' => $page,
            'page_config' => $all['pages'][$page] ?? $all['pages']['mall_home'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function mallData(): array
    {
        return self::pageData('mall_home');
    }

    /**
     * @return array<string, mixed>
     */
    public static function mallListingData(string $section): array
    {
        $all = self::all();

        return [
            ...$all,
            'section_key' => $section,
            'section_config' => $all['mall_sections'][$section] ?? null,
        ];
    }

    public static function applyMallSearch(Builder $query, string $section, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $fields = config("kabeeri_external.mall_sections.{$section}.search_fields", []);

        return $query->where(function (Builder $builder) use ($fields, $term): void {
            foreach ($fields as $index => $field) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $builder->{$method}($field, 'like', "%{$term}%");
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $config = config('kabeeri_external', []);

        return [
            'version' => $config['version'] ?? null,
            'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
            'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
            'v11_ready' => V11PublicExperience::isReleaseCandidateReady(),
            'v12_ready' => V12MarketplaceExperience::isReleaseCandidateReady(),
            'page_count' => count($config['pages'] ?? []),
            'mall_section_count' => count($config['mall_sections'] ?? []),
            'trust_badge_count' => count($config['trust_badges'] ?? []),
            'customer_step_count' => count($config['customer_steps'] ?? []),
            'partner_path_count' => count($config['partner_paths'] ?? []),
            'missing_routes' => self::missingRoutes(array_column($config['pages'] ?? [], 'route')),
            'missing_mall_routes' => self::missingRoutes(array_column($config['mall_sections'] ?? [], 'route')),
            'missing_admin_routes' => self::missingRoutes($config['admin_routes'] ?? []),
            'missing_database_tables' => array_values(array_filter(
                $config['database_tables'] ?? [],
                fn (string $table): bool => ! self::hasTable($table),
            )),
            'task_tracker_synced' => self::v13TaskTrackerSynced(),
            'docs' => [
                'external_ux' => file_exists(base_path('docs/kabeeri/ui/V13_MALL_CUSTOMER_PARTNER_UX.md')),
                'release_report' => file_exists(base_path('docs/kabeeri/ui/V13_RELEASE_CANDIDATE.md')),
            ],
            'smoke_tests' => file_exists(base_path('tests/Feature/V13ExternalExperienceTest.php')),
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V13'
            && $report['v9_ready']
            && $report['v10_ready']
            && $report['v11_ready']
            && $report['v12_ready']
            && $report['page_count'] >= 21
            && $report['mall_section_count'] >= 6
            && $report['trust_badge_count'] >= 4
            && $report['customer_step_count'] >= 5
            && $report['partner_path_count'] >= 4
            && $report['missing_routes'] === []
            && $report['missing_mall_routes'] === []
            && $report['missing_admin_routes'] === []
            && $report['missing_database_tables'] === []
            && $report['task_tracker_synced']
            && $report['smoke_tests']
            && ! in_array(false, $report['docs'], true);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function mallSections(): array
    {
        return collect(config('kabeeri_external.mall_sections', []))
            ->map(function (array $section): array {
                $model = $section['count_model'];
                $statusColumn = $section['status_column'];

                return $section + [
                    'count' => class_exists($model)
                        ? $model::query()->where($statusColumn, 'published')->count()
                        : 0,
                ];
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function liveInventory(): array
    {
        return [
            'tables' => array_map(
                fn (string $table): array => [
                    'name' => $table,
                    'exists' => self::hasTable($table),
                    'count' => self::safeCount($table),
                ],
                config('kabeeri_external.database_tables', []),
            ),
            'routes' => array_map(
                fn (array $page): array => [
                    'label' => $page['label'],
                    'route' => $page['route'],
                    'exists' => Route::has($page['route']),
                ],
                config('kabeeri_external.pages', []),
            ),
            'admin_routes' => array_map(
                fn (string $route): array => [
                    'route' => $route,
                    'exists' => Route::has($route),
                ],
                config('kabeeri_external.admin_routes', []),
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
                'v12_ready' => V12MarketplaceExperience::isReleaseCandidateReady(),
                'routes_ready' => self::missingRoutes(array_column(config('kabeeri_external.pages', []), 'route')) === [],
                'mall_routes_ready' => self::missingRoutes(array_column(config('kabeeri_external.mall_sections', []), 'route')) === [],
                'admin_routes_ready' => self::missingRoutes(config('kabeeri_external.admin_routes', [])) === [],
                'database_ready' => self::databaseTablesReady(),
                'docs_ready' => file_exists(base_path('docs/kabeeri/ui/V13_MALL_CUSTOMER_PARTNER_UX.md')),
                'smoke_tests_ready' => file_exists(base_path('tests/Feature/V13ExternalExperienceTest.php')),
                'tracker_synced' => self::v13TaskTrackerSynced(),
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, config('kabeeri_external.release_gates', []));

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V13ExternalExperienceTest',
                'php artisan test --filter=V12MarketplaceExperienceTest',
                'php artisan test --filter=PublicMallNavigationUxTest',
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
        return collect(config('kabeeri_external.database_tables', []))
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

    private static function v13TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v13.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 29 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }
}
