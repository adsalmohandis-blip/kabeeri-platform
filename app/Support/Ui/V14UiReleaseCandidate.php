<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\Route;

class V14UiReleaseCandidate
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_ui_quality', []);

        return [
            'config' => $config,
            'inventory' => self::routeInventory(),
            'coverage' => self::coverageReport(),
            'release' => self::releaseReadiness(),
            'validation' => self::validationReport(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $inventory = self::routeInventory();
        $coverage = self::coverageReport();
        $missingRoutes = collect($inventory['routes'])
            ->filter(fn (array $route): bool => ! $route['exists'])
            ->values()
            ->all();

        return [
            'version' => config('kabeeri_ui_quality.version'),
            'previous_versions' => [
                'v9' => V9UiFoundation::isReleaseCandidateReady(),
                'v10' => V10AdminExperience::isReleaseCandidateReady(),
                'v11' => V11PublicExperience::isReleaseCandidateReady(),
                'v12' => V12MarketplaceExperience::isReleaseCandidateReady(),
                'v13' => V13ExternalExperience::isReleaseCandidateReady(),
            ],
            'route_count' => count($inventory['routes']),
            'missing_routes' => $missingRoutes,
            'coverage' => $coverage,
            'docs' => [
                'qa_handoff' => file_exists(base_path('docs/kabeeri/ui/V14_UI_QA_HANDOFF.md')),
                'release_candidate' => file_exists(base_path('docs/kabeeri/ui/V14_UI_RELEASE_CANDIDATE.md')),
            ],
            'smoke_tests' => file_exists(base_path('tests/Feature/V14UiReleaseCandidateTest.php')),
            'task_tracker_synced' => self::v14TaskTrackerSynced(),
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V14'
            && ! in_array(false, $report['previous_versions'], true)
            && $report['route_count'] >= 70
            && $report['missing_routes'] === []
            && collect($report['coverage'])->every(fn (array $gate): bool => $gate['ready'])
            && ! in_array(false, $report['docs'], true)
            && $report['smoke_tests']
            && $report['task_tracker_synced'];
    }

    /**
     * @return array<string, mixed>
     */
    public static function routeInventory(): array
    {
        $routes = [];

        foreach (V9UiFoundation::currentRoutes() as $route) {
            $routes[] = self::routeItem('v9_foundation', (string) $route['route'], $route['key'] ?? $route['uri'] ?? 'V9 route');
        }

        foreach (config('kabeeri_admin.pages', []) as $page) {
            $routes[] = self::routeItem('v10_admin', $page['route'], $page['label']);
        }

        foreach (config('kabeeri_admin.quick_actions', []) as $action) {
            $routes[] = self::routeItem('v10_admin', $action['route'], $action['label']);
        }

        foreach (config('kabeeri_public.pages', []) as $page) {
            $routes[] = self::routeItem('v11_public', $page['route'], $page['label']);
        }

        $routes[] = self::routeItem('v11_public', 'public.contact.store', 'Contact Sales Store');

        foreach (config('kabeeri_marketplace.pages', []) as $page) {
            $routes[] = self::routeItem('v12_marketplace', $page['route'], $page['label']);
        }

        foreach (config('kabeeri_marketplace.admin_routes', []) as $route) {
            $routes[] = self::routeItem('v12_marketplace_admin', $route, $route);
        }

        foreach (config('kabeeri_external.pages', []) as $page) {
            $routes[] = self::routeItem('v13_external', $page['route'], $page['label']);
        }

        foreach (config('kabeeri_external.admin_routes', []) as $route) {
            $routes[] = self::routeItem('v13_external_admin', $route, $route);
        }

        $routes[] = self::routeItem('v14_quality', 'ui.release-candidate', 'V14 UI Release Candidate');

        return [
            'groups' => collect($routes)
                ->groupBy('group')
                ->map(fn ($items): array => [
                    'count' => $items->count(),
                    'missing' => $items->where('exists', false)->count(),
                ])
                ->all(),
            'routes' => $routes,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function coverageReport(): array
    {
        $accessibility = config('kabeeri_ui_quality.accessibility', []);
        $responsive = config('kabeeri_ui_quality.responsive', []);
        $security = config('kabeeri_ui_quality.security_permission', []);
        $states = config('kabeeri_ui_quality.states', []);
        $nextRuntime = config('kabeeri_ui_quality.next_runtime', []);
        $themeQa = config('kabeeri_marketplace.qa_checklists.theme', []);
        $pluginQa = config('kabeeri_marketplace.qa_checklists.plugin', []);
        $components = config('kabeeri_marketplace.component_library.components', []);

        return [
            'accessibility' => self::coverageGate(count($accessibility) >= 3, 'Admin, public, and form accessibility checklists are represented.'),
            'rtl_arabic' => self::coverageGate(
                config('kabeeri_public.rules.rtl_mobile') !== null && config('kabeeri_external.rules.runtime') !== null,
                'Arabic RTL and copy-quality expectations are represented in public/external rules.',
            ),
            'responsive' => self::coverageGate(count($responsive) >= 4, 'Mobile, tablet, desktop, and breakpoint coverage is represented.'),
            'navigation' => self::coverageGate(count(config('kabeeri_ui_quality.navigation.consistent_links', [])) >= 7, 'Cross-surface navigation consistency is represented.'),
            'states' => self::coverageGate(count($states['empty'] ?? []) >= 6 && count($states['feedback'] ?? []) >= 3, 'Empty, error, loading, and feedback states are represented.'),
            'security_permission' => self::coverageGate(count($security) >= 3, 'Admin, Marketplace, and Mall permission/security UX is represented.'),
            'performance_assets' => self::coverageGate(file_exists(public_path('build/manifest.json')), 'Vite build manifest exists.'),
            'manual_cross_browser' => self::coverageGate(count(config('kabeeri_ui_quality.manual_qa.browsers', [])) >= 3, 'Cross-browser manual QA checklist is represented.'),
            'next_runtime' => self::coverageGate(count($nextRuntime['compliance_checks'] ?? []) >= 4, 'Next.js separation compliance checks are represented.'),
            'permission_navigation' => self::coverageGate(count(config('kabeeri_admin.permission_aware_navigation.blocked_actions', [])) >= 6, 'Permission-aware admin navigation is represented.'),
            'progressive_disclosure' => self::coverageGate(count(config('kabeeri_public.wizard.progressive_rules', [])) >= 4, 'Progressive disclosure rules are represented.'),
            'design_system' => self::coverageGate(count($components) >= 10 && count(V9UiFoundation::designTokens()['colors'] ?? []) >= 3, 'Design tokens and V12 component library coverage are represented.'),
            'theme_qa' => self::coverageGate(count($themeQa) >= 10, 'Theme QA checklist coverage is represented.'),
            'plugin_qa' => self::coverageGate(count($pluginQa) >= 12, 'Plugin QA checklist coverage is represented.'),
            'mall_marketplace_separation' => self::coverageGate(
                str_contains(config('kabeeri_external.rules.mall_vs_marketplace', ''), 'kabeeri Mall is public discovery')
                && str_contains(config('kabeeri_marketplace.rules.marketplace_vs_mall', ''), 'kabeeri Marketplace sells'),
                'Marketplace vs Mall separation is represented in V12 and V13.',
            ),
            'product_narrative' => self::coverageGate(count(config('kabeeri_public.story_layers', [])) >= 6, 'Progressive public product narrative is represented.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function releaseReadiness(): array
    {
        $report = self::validationReport();
        $gates = array_map(function (array $gate) use ($report): array {
            $ready = match ($gate['key']) {
                'all_previous_versions_ready' => ! in_array(false, $report['previous_versions'], true),
                'route_inventory_ready' => $report['missing_routes'] === [] && $report['route_count'] >= 70,
                'accessibility_ready' => $report['coverage']['accessibility']['ready'],
                'responsive_ready' => $report['coverage']['responsive']['ready'],
                'permission_ready' => $report['coverage']['security_permission']['ready'] && $report['coverage']['permission_navigation']['ready'],
                'design_system_ready' => $report['coverage']['design_system']['ready'],
                'theme_plugin_qa_ready' => $report['coverage']['theme_qa']['ready'] && $report['coverage']['plugin_qa']['ready'],
                'mall_marketplace_separated' => $report['coverage']['mall_marketplace_separation']['ready'],
                'docs_ready' => ! in_array(false, $report['docs'], true),
                'tests_ready' => $report['smoke_tests'],
                'tracker_synced' => $report['task_tracker_synced'],
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, config('kabeeri_ui_quality.quality_gates', []));

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V14UiReleaseCandidateTest',
                'php artisan test --filter=V13ExternalExperienceTest',
                'php artisan test --filter=V12MarketplaceExperienceTest',
                'vendor/bin/pint --test',
                'npm run build',
                'php artisan test',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function routeItem(string $group, string $route, string|int $label): array
    {
        return [
            'group' => $group,
            'route' => $route,
            'label' => (string) $label,
            'exists' => Route::has($route),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function coverageGate(bool $ready, string $note): array
    {
        return [
            'ready' => $ready,
            'note' => $note,
        ];
    }

    private static function v14TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v14.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 28 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }
}
