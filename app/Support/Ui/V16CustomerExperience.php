<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\Route;

class V16CustomerExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $routes = collect(config('kabeeri_customer.routes', []))
            ->mapWithKeys(fn (array $route): array => [$route['name'] => Route::has($route['name'])])
            ->all();

        return [
            'version' => config('kabeeri_customer.version'),
            'routes' => $routes,
            'config' => [
                'paths' => count(config('kabeeri_customer.audience_paths', [])) >= 6,
                'app_types' => count(config('kabeeri_customer.app_types', [])) >= 6,
                'starter_themes' => count(config('kabeeri_customer.starter_themes', [])) >= 3,
                'capabilities' => count(config('kabeeri_customer.capabilities', [])) >= 5,
            ],
            'files' => [
                'auth_controller' => file_exists(app_path('Http/Controllers/Web/CustomerAuthController.php')),
                'start_controller' => file_exists(app_path('Http/Controllers/Web/CustomerStartController.php')),
                'workspace_controller' => file_exists(app_path('Http/Controllers/Web/CustomerWorkspaceController.php')),
                'service' => file_exists(app_path('Modules/Core/Services/CustomerWorkspaceService.php')),
                'start_view' => file_exists(resource_path('views/customer/v16-start.blade.php')),
                'auth_view' => file_exists(resource_path('views/customer/v16-auth.blade.php')),
                'onboarding_view' => file_exists(resource_path('views/customer/v16-onboarding.blade.php')),
                'dashboard_view' => file_exists(resource_path('views/customer/v16-dashboard.blade.php')),
                'site_view' => file_exists(resource_path('views/customer/v16-site.blade.php')),
            ],
            'docs' => [
                'customer_onboarding' => file_exists(base_path('docs/kabeeri/ui/V16_CUSTOMER_ONBOARDING.md')),
                'release_candidate' => file_exists(base_path('docs/kabeeri/ui/V16_RELEASE_CANDIDATE.md')),
            ],
            'tests' => file_exists(base_path('tests/Feature/V16CustomerExperienceTest.php')),
            'task_tracker_synced' => self::v16TaskTrackerSynced(),
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V16'
            && ! in_array(false, $report['routes'], true)
            && ! in_array(false, $report['config'], true)
            && ! in_array(false, $report['files'], true)
            && ! in_array(false, $report['docs'], true)
            && $report['tests']
            && $report['task_tracker_synced'];
    }

    private static function v16TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v16.tasks.json');
        if (! file_exists($path)) {
            return false;
        }

        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 17 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }
}
