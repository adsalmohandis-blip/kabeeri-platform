<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class V10AdminExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_admin', []);

        return [
            'config' => $config,
            'system' => self::systemCheck(),
            'tasks' => self::taskTracker(),
            'database' => self::databaseStatus(),
            'modules' => self::moduleHealth(),
            'release' => self::releaseReadiness(),
            'workspaces' => self::workspaces(),
            'quick_actions' => self::quickActions(),
            'permission_navigation' => $config['permission_aware_navigation'] ?? [],
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
            'page_config' => $all['config']['pages'][$page] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $config = config('kabeeri_admin', []);
        $pageRoutes = array_column($config['pages'] ?? [], 'route');
        $missingPageRoutes = array_values(array_filter(
            $pageRoutes,
            fn (string $route): bool => ! Route::has($route),
        ));
        $missingQuickActionRoutes = array_values(array_filter(
            array_column($config['quick_actions'] ?? [], 'route'),
            fn (string $route): bool => ! Route::has($route),
        ));

        return [
            'version' => $config['version'] ?? null,
            'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
            'page_count' => count($config['pages'] ?? []),
            'module_group_count' => count($config['module_groups'] ?? []),
            'workspace_count' => count($config['workspace_homes'] ?? []),
            'quick_action_count' => count($config['quick_actions'] ?? []),
            'blocked_action_count' => count($config['permission_aware_navigation']['blocked_actions'] ?? []),
            'missing_page_routes' => $missingPageRoutes,
            'missing_quick_action_routes' => $missingQuickActionRoutes,
            'docs' => [
                'admin_ux' => file_exists(base_path('docs/kabeeri/ui/V10_ADMIN_UX.md')),
                'release_report' => file_exists(base_path('docs/kabeeri/ui/V10_RELEASE_CANDIDATE.md')),
            ],
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V10'
            && $report['v9_ready']
            && $report['page_count'] >= 6
            && $report['module_group_count'] >= 10
            && $report['workspace_count'] >= 12
            && $report['quick_action_count'] >= 6
            && $report['blocked_action_count'] >= 6
            && $report['missing_page_routes'] === []
            && $report['missing_quick_action_routes'] === []
            && ! in_array(false, $report['docs'], true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function systemCheck(): array
    {
        $checks = [
            self::check('Laravel application', true, app()->environment(), 'Application container is booted.'),
            self::check('V9 UI foundation', V9UiFoundation::isReleaseCandidateReady(), 'Ready', 'UI boundaries and tokens are available.'),
            self::check('Database connection', self::databaseResponds(), config('database.default'), 'Connection responds to a simple query.'),
            self::check('Task tracker files', count(glob(base_path('24_kabeeri_task_tracking/tasks/*.tasks.json')) ?: []) >= 10, 'V1-V14 + add-ons', 'Task files are available for admin visibility.'),
            self::check('Filament resources', self::countDirectories(app_path('Filament/Resources')) > 0, (string) self::countDirectories(app_path('Filament/Resources')), 'Admin resources discovered in code.'),
            self::check('V10 docs', file_exists(base_path('docs/kabeeri/ui/V10_ADMIN_UX.md')), 'Documented', 'Admin UX rules and release criteria are documented.'),
        ];

        return [
            'status' => collect($checks)->every(fn (array $check): bool => $check['ok']) ? 'ready' : 'needs_attention',
            'checks' => $checks,
            'inventory' => [
                ['label' => 'Migrations', 'value' => self::countFiles(database_path('migrations'), 'php')],
                ['label' => 'Models', 'value' => self::countFiles(app_path('Models'), 'php')],
                ['label' => 'Filament Resources', 'value' => self::countDirectories(app_path('Filament/Resources'))],
                ['label' => 'Feature Tests', 'value' => self::countFiles(base_path('tests/Feature'), 'php')],
                ['label' => 'UI Docs', 'value' => self::countFiles(base_path('docs/kabeeri/ui'), 'md')],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function taskTracker(): array
    {
        $files = glob(base_path('24_kabeeri_task_tracking/tasks/*.tasks.json')) ?: [];
        usort($files, fn (string $a, string $b): int => self::taskVersionOrder($a) <=> self::taskVersionOrder($b));

        $versions = [];
        $summary = [
            'total' => 0,
            'done' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'blocked' => 0,
            'verified' => 0,
        ];

        foreach ($files as $file) {
            $payload = json_decode((string) file_get_contents($file), true);
            $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];
            $counts = array_count_values(array_map(fn (array $task): string => (string) ($task['status'] ?? 'pending'), $tasks));
            $total = count($tasks);
            $done = (int) (($counts['codex_done'] ?? 0) + ($counts['verified'] ?? 0));
            $key = str_replace('.tasks.json', '', basename($file));

            $versions[] = [
                'key' => $key,
                'name' => self::taskVersionName($key),
                'total' => $total,
                'done' => $done,
                'pending' => (int) ($counts['pending'] ?? 0),
                'in_progress' => (int) ($counts['in_progress'] ?? 0),
                'blocked' => (int) ($counts['blocked'] ?? 0),
                'verified' => (int) ($counts['verified'] ?? 0),
                'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            ];

            $summary['total'] += $total;
            $summary['done'] += $done;
            $summary['pending'] += (int) ($counts['pending'] ?? 0);
            $summary['in_progress'] += (int) ($counts['in_progress'] ?? 0);
            $summary['blocked'] += (int) ($counts['blocked'] ?? 0);
            $summary['verified'] += (int) ($counts['verified'] ?? 0);
        }

        $summary['percent'] = $summary['total'] > 0 ? (int) round(($summary['done'] / $summary['total']) * 100) : 0;

        return [
            'summary' => $summary,
            'versions' => $versions,
            'latest_history' => self::latestHistory(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function databaseStatus(): array
    {
        $migrationFiles = self::countFiles(database_path('migrations'), 'php');
        $appliedMigrations = self::hasTable('migrations') ? self::safeCount('migrations') : 0;

        return [
            'connection' => config('database.default'),
            'responds' => self::databaseResponds(),
            'migration_files' => $migrationFiles,
            'applied_migrations' => $appliedMigrations,
            'pending_migration_estimate' => max(0, $migrationFiles - $appliedMigrations),
            'groups' => array_values(array_map(
                fn (array $group): array => [
                    'label' => $group['label'],
                    'tables' => array_map(
                        fn (string $table): array => [
                            'name' => $table,
                            'exists' => self::hasTable($table),
                            'count' => self::safeCount($table),
                        ],
                        $group['tables'],
                    ),
                ],
                config('kabeeri_admin.module_groups', []),
            )),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function moduleHealth(): array
    {
        return array_values(array_map(function (array $group): array {
            $tables = array_map(
                fn (string $table): array => [
                    'name' => $table,
                    'exists' => self::hasTable($table),
                    'count' => self::safeCount($table),
                ],
                $group['tables'],
            );
            $existingTables = count(array_filter($tables, fn (array $table): bool => $table['exists']));
            $totalRecords = array_sum(array_column($tables, 'count'));

            return [
                'label' => $group['label'],
                'health' => $existingTables === count($tables) ? 'ready' : 'partial',
                'existing_tables' => $existingTables,
                'table_count' => count($tables),
                'records' => $totalRecords,
                'routes' => array_map(
                    fn (string $route): array => [
                        'name' => $route,
                        'exists' => Route::has($route),
                    ],
                    $group['routes'],
                ),
                'next_action' => $group['next_action'],
            ];
        }, config('kabeeri_admin.module_groups', [])));
    }

    /**
     * @return array<string, mixed>
     */
    private static function releaseReadiness(): array
    {
        $gates = array_map(function (array $gate): array {
            $ready = match ($gate['key']) {
                'v9_foundation_ready' => V9UiFoundation::isReleaseCandidateReady(),
                'v10_pages_registered' => self::v10PageRoutesReady(),
                'task_tracker_synced' => self::v10TaskTrackerSynced(),
                'docs_ready' => file_exists(base_path('docs/kabeeri/ui/V10_ADMIN_UX.md')),
                'smoke_tests_ready' => file_exists(base_path('tests/Feature/V10AdminExperienceTest.php')),
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, config('kabeeri_admin.release_gates', []));

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V10AdminExperienceTest',
                'php artisan test --filter=AdminPanelTest',
                'vendor/bin/pint --test',
                'npm run build',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function workspaces(): array
    {
        $spaces = config('kabeeri_ui.admin_spaces', []);
        $homes = config('kabeeri_admin.workspace_homes', []);

        return array_values(array_map(function (string $key, array $space) use ($homes): array {
            $home = $homes[$key] ?? [];

            return [
                'key' => $key,
                'label' => $home['label'] ?? $space['label'],
                'context' => $home['context'] ?? $space['context'],
                'primary_route' => $home['primary_route'] ?? 'filament.admin.pages.admin-workspaces',
                'route_exists' => Route::has($home['primary_route'] ?? 'filament.admin.pages.admin-workspaces'),
                'primary_jobs' => $space['primary_jobs'] ?? [],
                'resource_groups' => $space['resource_groups'] ?? [],
                'empty_state' => $home['empty_state'] ?? 'No active work in this space yet.',
            ];
        }, array_keys($spaces), $spaces));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function quickActions(): array
    {
        return array_values(array_map(
            fn (array $action): array => $action + [
                'route_exists' => Route::has($action['route']),
                'blocked' => ($action['intent'] ?? null) === 'permission_required',
            ],
            config('kabeeri_admin.quick_actions', []),
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function latestHistory(): array
    {
        $path = base_path('24_kabeeri_task_tracking/logs/task_history.jsonl');

        if (! file_exists($path)) {
            return [];
        }

        return array_values(array_filter(array_map(function (string $line): ?array {
            $payload = json_decode($line, true);

            if (! is_array($payload)) {
                return null;
            }

            return [
                'at' => $payload['at'] ?? '',
                'version' => $payload['version'] ?? '',
                'task_id' => $payload['task_id'] ?? '',
                'status' => $payload['status'] ?? '',
                'notes' => $payload['notes'] ?? '',
            ];
        }, array_reverse(array_slice(file($path, FILE_IGNORE_NEW_LINES) ?: [], -10)))));
    }

    /**
     * @return array<string, mixed>
     */
    private static function check(string $label, bool $ok, string $value, string $note): array
    {
        return compact('label', 'ok', 'value', 'note');
    }

    private static function databaseResponds(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private static function v10PageRoutesReady(): bool
    {
        return collect(config('kabeeri_admin.pages', []))
            ->every(fn (array $page): bool => Route::has($page['route']));
    }

    private static function v10TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v10.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 29 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
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
        if (! self::hasTable($table)) {
            return 0;
        }

        try {
            return DB::table($table)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private static function countFiles(string $path, string $extension): int
    {
        return is_dir($path) ? count(glob($path.'/*.'.$extension) ?: []) : 0;
    }

    private static function countDirectories(string $path): int
    {
        return is_dir($path) ? count(array_filter(glob($path.'/*') ?: [], 'is_dir')) : 0;
    }

    private static function taskVersionOrder(string $file): int
    {
        $key = str_replace('.tasks.json', '', basename($file));

        if (preg_match('/^v(\d+)$/', $key, $matches)) {
            return (int) $matches[1];
        }

        return match ($key) {
            'freemium' => 90,
            'ext_update' => 91,
            default => 99,
        };
    }

    private static function taskVersionName(string $key): string
    {
        if (preg_match('/^v(\d+)$/', $key, $matches)) {
            return 'V'.$matches[1];
        }

        return strtoupper($key);
    }
}
