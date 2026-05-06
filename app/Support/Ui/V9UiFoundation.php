<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\Route;

class V9UiFoundation
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return config('kabeeri_ui', []);
    }

    /**
     * @return array<string, mixed>
     */
    public static function designTokens(): array
    {
        return self::all()['design_tokens'] ?? [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function adminSpaces(): array
    {
        return self::all()['admin_spaces'] ?? [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function externalAudiences(): array
    {
        return self::all()['external_audiences'] ?? [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function currentRoutes(): array
    {
        $registry = self::all()['route_registry'] ?? [];
        $groups = [
            'current_laravel_public',
            'current_admin',
            'api_contract_candidates',
        ];

        $routes = [];

        foreach ($groups as $group) {
            foreach ($registry[$group] ?? [] as $route) {
                if (($route['status'] ?? null) !== 'current') {
                    continue;
                }

                $routes[] = $route + ['registry_group' => $group];
            }
        }

        return $routes;
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $tokens = self::designTokens();
        $spaces = self::adminSpaces();
        $audiences = self::externalAudiences();
        $missingRoutes = array_values(array_filter(
            self::currentRoutes(),
            fn (array $route): bool => ! Route::has((string) ($route['route'] ?? '')),
        ));

        return [
            'version' => self::all()['version'] ?? null,
            'has_runtime_decision' => filled(self::all()['decision']['public_runtime_target'] ?? null),
            'has_design_tokens' => isset($tokens['colors'], $tokens['typography'], $tokens['spacing'], $tokens['radius']),
            'admin_space_count' => count($spaces),
            'external_audience_count' => count($audiences),
            'current_route_count' => count(self::currentRoutes()),
            'missing_routes' => $missingRoutes,
            'docs' => [
                'foundation' => file_exists(base_path('docs/kabeeri/ui/V9_UI_FOUNDATION.md')),
                'registry' => file_exists(base_path('docs/kabeeri/ui/ROUTE_PAGE_REGISTRY.md')),
                'smoke_strategy' => file_exists(base_path('docs/kabeeri/ui/UI_SMOKE_TEST_STRATEGY.md')),
                'next_runtime' => file_exists(base_path('docs/kabeeri/ui/NEXT_PUBLIC_RUNTIME_PLAN.md')),
            ],
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V9'
            && $report['has_runtime_decision']
            && $report['has_design_tokens']
            && $report['admin_space_count'] >= 12
            && $report['external_audience_count'] >= 6
            && $report['current_route_count'] >= 10
            && $report['missing_routes'] === []
            && ! in_array(false, $report['docs'], true);
    }
}
