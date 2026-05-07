<?php

namespace App\Support\Ui;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class V11PublicExperience
{
    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $config = config('kabeeri_public', []);

        return [
            'config' => $config,
            'pages' => $config['pages'] ?? [],
            'story_layers' => $config['story_layers'] ?? [],
            'audiences' => $config['audiences'] ?? [],
            'journeys' => $config['journeys'] ?? [],
            'onboarding_steps' => $config['onboarding_steps'] ?? [],
            'wizard' => $config['wizard'] ?? [],
            'pricing_layers' => $config['pricing_layers'] ?? [],
            'plans' => $config['plans'] ?? [],
            'templates' => $config['templates'] ?? [],
            'faq' => $config['faq'] ?? [],
            'next_runtime' => $config['next_runtime'] ?? [],
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
            'page_config' => $all['pages'][$page] ?? $all['pages']['landing'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function captureInquiry(array $payload): Lead
    {
        $organization = self::publicInquiryOrganization();
        $companyName = $payload['company_name'] ?? null;

        return Lead::query()->create([
            'organization_id' => $organization->id,
            'title' => 'Public inquiry: '.($companyName ?: $payload['name']),
            'company_name' => $companyName ?: null,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? null,
            'source' => 'public_v11_inquiry',
            'status' => 'new',
            'priority' => match ($payload['audience'] ?? null) {
                'enterprise' => 'high',
                'agency', 'developers', 'developer_creator' => 'normal',
                default => 'normal',
            },
            'message' => $payload['message'],
            'metadata' => [
                'audience' => $payload['audience'] ?? 'unknown',
                'plan_interest' => $payload['plan_interest'] ?? null,
                'source_page' => 'V11 Contact Sales',
                'next_step' => 'Review inquiry in CRM leads.',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationReport(): array
    {
        $config = config('kabeeri_public', []);
        $routes = array_column($config['pages'] ?? [], 'route');
        $missingRoutes = array_values(array_filter(
            $routes,
            fn (string $route): bool => ! Route::has($route),
        ));

        return [
            'version' => $config['version'] ?? null,
            'v9_ready' => V9UiFoundation::isReleaseCandidateReady(),
            'v10_ready' => V10AdminExperience::isReleaseCandidateReady(),
            'page_count' => count($config['pages'] ?? []),
            'audience_count' => count($config['audiences'] ?? []),
            'story_layer_count' => count($config['story_layers'] ?? []),
            'pricing_layer_count' => count($config['pricing_layers'] ?? []),
            'missing_routes' => $missingRoutes,
            'task_tracker_synced' => self::v11TaskTrackerSynced(),
            'docs' => [
                'public_ux' => file_exists(base_path('docs/kabeeri/ui/V11_PUBLIC_UX.md')),
                'release_report' => file_exists(base_path('docs/kabeeri/ui/V11_RELEASE_CANDIDATE.md')),
            ],
        ];
    }

    public static function isReleaseCandidateReady(): bool
    {
        $report = self::validationReport();

        return $report['version'] === 'V11'
            && $report['v9_ready']
            && $report['v10_ready']
            && $report['page_count'] >= 14
            && $report['audience_count'] >= 5
            && $report['story_layer_count'] >= 6
            && $report['pricing_layer_count'] >= 6
            && $report['missing_routes'] === []
            && $report['task_tracker_synced']
            && ! in_array(false, $report['docs'], true);
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
                'routes_ready' => self::routesReady(),
                'inquiry_flow_ready' => class_exists(Lead::class) && class_exists(Organization::class),
                'docs_ready' => file_exists(base_path('docs/kabeeri/ui/V11_PUBLIC_UX.md')),
                'smoke_tests_ready' => file_exists(base_path('tests/Feature/V11PublicExperienceTest.php')),
                default => false,
            };

            return $gate + ['ready' => $ready];
        }, config('kabeeri_public.release_gates', []));

        return [
            'ready' => collect($gates)->every(fn (array $gate): bool => $gate['ready']),
            'gates' => $gates,
            'commands' => [
                'php artisan test --filter=V11PublicExperienceTest',
                'php artisan test --filter=RootDashboardPageTest',
                'vendor/bin/pint --test',
                'npm run build',
            ],
        ];
    }

    private static function routesReady(): bool
    {
        return collect(config('kabeeri_public.pages', []))
            ->every(fn (array $page): bool => Route::has($page['route']));
    }

    private static function v11TaskTrackerSynced(): bool
    {
        $path = base_path('24_kabeeri_task_tracking/tasks/v11.tasks.json');
        $payload = json_decode((string) file_get_contents($path), true);
        $tasks = is_array($payload['tasks'] ?? null) ? $payload['tasks'] : [];

        return count($tasks) >= 27 && collect($tasks)->every(
            fn (array $task): bool => in_array(($task['status'] ?? null), ['codex_done', 'verified'], true),
        );
    }

    private static function publicInquiryOrganization(): Organization
    {
        $contact = config('kabeeri_public.contact');
        $owner = User::query()->firstOrCreate(
            ['email' => $contact['owner_email']],
            [
                'name' => 'kabeeri Public Inquiry Owner',
                'password' => Str::password(40),
            ],
        );

        return Organization::query()->firstOrCreate(
            ['slug' => $contact['organization_slug']],
            [
                'name' => $contact['organization_name'],
                'owner_user_id' => $owner->id,
                'account_type' => 'platform_internal',
                'status' => 'active',
                'plan_code' => 'enterprise',
                'locale' => 'ar',
                'timezone' => 'Africa/Cairo',
                'metadata' => ['source' => 'v11_public_inquiries'],
            ],
        );
    }
}
