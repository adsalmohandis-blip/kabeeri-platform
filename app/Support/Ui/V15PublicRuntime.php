<?php

namespace App\Support\Ui;

use Illuminate\Support\Facades\Route;

class V15PublicRuntime
{
    /**
     * @return array<string, mixed>
     */
    public static function manifest(): array
    {
        return [
            'version' => config('kabeeri_public_runtime.version'),
            'contract' => config('kabeeri_public_runtime.api.contract_version'),
            'runtime' => config('kabeeri_public_runtime.workspace'),
            'rules' => config('kabeeri_public_runtime.rules'),
            'routes' => config('kabeeri_public_runtime.routes'),
            'audience_paths' => config('kabeeri_public_runtime.audience_paths'),
            'theme_manifest' => config('kabeeri_public_runtime.theme_manifest'),
            'api' => config('kabeeri_public_runtime.api'),
            'quality_gates' => self::qualityReport(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $rootPackage = self::packageJson(base_path('package.json'));
        $workspacePackage = self::packageJson(base_path('apps/public-web/package.json'));
        $requiredFiles = config('kabeeri_public_runtime.required_files', []);

        return [
            'version' => config('kabeeri_public_runtime.version'),
            'v14_ready' => V14UiReleaseCandidate::isReleaseCandidateReady(),
            'manifest_route_exists' => Route::has((string) config('kabeeri_public_runtime.api.manifest_route')),
            'workspace_exists' => is_dir(base_path((string) config('kabeeri_public_runtime.workspace.path'))),
            'required_files' => collect($requiredFiles)
                ->mapWithKeys(fn (string $file): array => [$file => file_exists(base_path($file))])
                ->all(),
            'root_scripts' => [
                'public-web:build' => isset($rootPackage['scripts']['public-web:build']),
                'public-web:typecheck' => isset($rootPackage['scripts']['public-web:typecheck']),
                'public-web:smoke' => isset($rootPackage['scripts']['public-web:smoke']),
            ],
            'workspace_scripts' => [
                'build' => isset($workspacePackage['scripts']['build']),
                'typecheck' => isset($workspacePackage['scripts']['typecheck']),
                'test:smoke' => isset($workspacePackage['scripts']['test:smoke']),
            ],
            'docs' => [
                'runtime' => file_exists(base_path('docs/kabeeri/ui/V15_NEXT_PUBLIC_RUNTIME.md')),
                'release_candidate' => file_exists(base_path('docs/kabeeri/ui/V15_RELEASE_CANDIDATE.md')),
            ],
            'task_tracker_synced' => self::v15TaskTrackerSynced(),
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V15'
            && $report['v14_ready']
            && $report['manifest_route_exists']
            && $report['workspace_exists']
            && ! in_array(false, $report['required_files'], true)
            && ! in_array(false, $report['root_scripts'], true)
            && ! in_array(false, $report['workspace_scripts'], true)
            && ! in_array(false, $report['docs'], true)
            && $report['task_tracker_synced'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function qualityReport(): array
    {
        $report = self::validationReport();

        return collect(config('kabeeri_public_runtime.quality_gates', []))
            ->map(function (array $gate) use ($report): array {
                $ready = match ($gate['key']) {
                    'v14_ready' => $report['v14_ready'],
                    'manifest_route_ready' => $report['manifest_route_exists'],
                    'workspace_ready' => $report['workspace_exists'],
                    'next_files_ready' => ! in_array(false, $report['required_files'], true),
                    'package_scripts_ready' => ! in_array(false, $report['root_scripts'], true) && ! in_array(false, $report['workspace_scripts'], true),
                    'docs_ready' => ! in_array(false, $report['docs'], true),
                    'tracker_synced' => $report['task_tracker_synced'],
                    default => false,
                };

                return $gate + ['ready' => $ready];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function packageJson(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $payload = json_decode((string) file_get_contents($path), true);

        return is_array($payload) ? $payload : [];
    }

    private static function v15TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v15.tasks.json');
        if (! file_exists($path)) {
            return false;
        }

        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 22 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }
}
