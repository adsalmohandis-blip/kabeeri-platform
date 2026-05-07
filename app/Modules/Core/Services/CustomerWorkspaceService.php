<?php

namespace App\Modules\Core\Services;

use App\Models\Organization;
use App\Models\Site;
use App\Models\Theme;
use App\Models\ThemeAppRecipe;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CustomerWorkspaceService
{
    public function __construct(
        protected OnboardingService $onboarding,
        protected ThemeRegistryService $themes,
        protected DemoImportService $demoImporter,
    ) {}

    /**
     * @return Collection<int, Theme>
     */
    public function starterThemes(?string $appType = null): Collection
    {
        $this->ensureStarterThemes();

        return Theme::query()
            ->where('status', 'active')
            ->orderByDesc('performance_score')
            ->get()
            ->filter(function (Theme $theme) use ($appType): bool {
                if ($appType === null || $appType === '') {
                    return true;
                }

                return in_array($appType, $theme->app_types ?? [], true);
            })
            ->values();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{organization: Organization, site: Site, theme: Theme, profile: UserProfile}
     */
    public function provision(User $user, array $data): array
    {
        $this->ensureStarterThemes();

        $path = (string) ($data['customer_path'] ?? 'business_owner');
        $pathConfig = config("kabeeri_customer.audience_paths.{$path}");

        if (! is_array($pathConfig)) {
            throw new InvalidArgumentException('Invalid customer path.');
        }

        $appType = (string) ($data['app_type'] ?? $pathConfig['default_app_type'] ?? 'website');
        $theme = $this->resolveTheme((string) ($data['theme_slug'] ?? ''), $appType);
        $organizationName = trim((string) ($data['organization_name'] ?? ''));
        $siteName = trim((string) ($data['site_name'] ?? ''));

        if ($organizationName === '') {
            throw new InvalidArgumentException('organization_name is required.');
        }

        if ($siteName === '') {
            throw new InvalidArgumentException('site_name is required.');
        }

        return DB::transaction(function () use ($user, $data, $path, $pathConfig, $appType, $theme, $organizationName, $siteName): array {
            $workspace = $this->onboarding->createFirstWorkspace($user, [
                'organization_name' => $organizationName,
                'organization_account_type' => $path,
                'site_name' => $siteName,
                'site_type' => $this->siteTypeFor($appType),
                'language' => (string) ($data['language'] ?? 'ar'),
                'locale' => (string) ($data['locale'] ?? 'ar'),
                'timezone' => (string) ($data['timezone'] ?? 'Africa/Cairo'),
                'create_company_draft' => (bool) ($pathConfig['creates_company'] ?? true),
                'company_name' => (string) ($data['company_name'] ?? $organizationName),
                'seed_basic_cms_page' => true,
                'seed_page_title' => 'Home',
                'seed_page_body' => 'Welcome to your Kabeeri app. This starter page was created by V16 onboarding.',
            ]);

            /** @var Organization $organization */
            $organization = $workspace['organization'];
            /** @var Site $site */
            $site = $workspace['site'];

            $this->themes->assignToSite($site, $theme);
            $this->applyThemeSettings($site->refresh(), $theme, $path, $appType, $data);
            $this->importRecipeContent($site->refresh(), $theme, $appType);
            $profile = $this->syncProfile($user, $path, $appType, $data);

            $organization->forceFill([
                'metadata' => array_merge($organization->metadata ?? [], [
                    'v16_customer_path' => $path,
                    'v16_app_type' => $appType,
                    'v16_theme_slug' => $theme->slug,
                    'needs_builder_help' => (bool) ($data['needs_builder_help'] ?? $path === 'needs_builder'),
                    'builder_request_note' => $data['builder_request_note'] ?? null,
                ]),
            ])->save();

            $site->forceFill([
                'metadata' => array_merge($site->metadata ?? [], [
                    'v16_customer_path' => $path,
                    'v16_app_type' => $appType,
                    'theme_installed_at' => now()->toISOString(),
                    'theme_install_status' => 'active',
                ]),
            ])->save();

            return [
                'organization' => $organization->refresh(),
                'site' => $site->refresh(),
                'theme' => $theme->refresh(),
                'profile' => $profile->refresh(),
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(User $user): array
    {
        $organizations = $user->ownedOrganizations()
            ->with(['sites.theme', 'companies'])
            ->latest('id')
            ->get();
        $activeOrganization = $organizations->first();
        $sites = $activeOrganization?->sites ?? collect();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id], [
            'visibility' => 'private',
            'metadata' => ['capabilities' => ['customer_owner']],
        ]);

        return [
            'user' => $user,
            'profile' => $profile,
            'organizations' => $organizations,
            'active_organization' => $activeOrganization,
            'sites' => $sites,
            'cards' => config('kabeeri_customer.dashboard_cards', []),
            'capabilities' => config('kabeeri_customer.capabilities', []),
        ];
    }

    /**
     * @param  array<int, string>  $capabilities
     */
    public function updateCapabilities(User $user, array $capabilities, ?string $note = null): UserProfile
    {
        $allowed = array_keys(config('kabeeri_customer.capabilities', []));
        $selected = array_values(array_unique(array_filter(
            $capabilities,
            fn (string $capability): bool => in_array($capability, $allowed, true),
        )));

        if (! in_array('customer_owner', $selected, true)) {
            array_unshift($selected, 'customer_owner');
        }

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id], [
            'visibility' => 'private',
            'metadata' => [],
        ]);
        $metadata = $profile->metadata ?? [];
        $metadata['capabilities'] = $selected;
        $metadata['capability_note'] = $note;
        $metadata['capabilities_updated_at'] = now()->toISOString();

        $profile->forceFill(['metadata' => $metadata])->save();

        return $profile->refresh();
    }

    public function ensureStarterThemes(): void
    {
        foreach (config('kabeeri_customer.starter_themes', []) as $theme) {
            $registered = $this->themes->register([
                'name' => $theme['name'],
                'slug' => $theme['slug'],
                'version' => '1.0.0',
                'publisher' => $theme['publisher'] ?? 'KABEERI Official',
                'status' => 'active',
                'type' => 'official',
                'category' => $theme['category'] ?? 'business website',
                'industries' => ['general'],
                'app_types' => $theme['app_types'] ?? ['website'],
                'price_type' => $theme['price_type'] ?? 'free',
                'demo_url' => $theme['demo_url'] ?? null,
                'preview_images' => $theme['preview_images'] ?? [],
                'performance_score' => $theme['performance_score'] ?? 90,
                'compatibility' => ['v16_customer_onboarding', 'blade_bridge', 'next_public_runtime_ready'],
                'supports_rtl' => $theme['supports_rtl'] ?? true,
                'supports_dark_mode' => $theme['supports_dark_mode'] ?? false,
                'manifest' => $theme['manifest'] ?? [],
                'metadata' => ['source' => 'v16_customer_defaults', 'settings' => $theme['settings'] ?? []],
            ]);

            foreach ($theme['app_types'] ?? ['website'] as $appType) {
                ThemeAppRecipe::query()->updateOrCreate(
                    [
                        'theme_id' => $registered->id,
                        'project_type' => 'customer_onboarding',
                        'app_type' => $appType,
                    ],
                    [
                        'required_packages' => ['cms'],
                        'recommended_packages' => $appType === 'store' ? ['commerce-lite', 'forms'] : ['forms'],
                        'optional_packages' => ['crm', 'mall-visibility'],
                        'demo_content_ref' => 'v16-'.$appType,
                        'setup_steps' => ['Create workspace', 'Install theme', 'Seed starter content', 'Open customer dashboard'],
                        'metadata' => ['source' => 'v16_customer_defaults'],
                    ],
                );
            }
        }
    }

    protected function resolveTheme(string $slug, string $appType): Theme
    {
        $theme = $slug !== '' ? Theme::query()->where('slug', $slug)->first() : null;

        if ($theme && in_array($appType, $theme->app_types ?? [], true)) {
            return $theme;
        }

        $fallback = $this->starterThemes($appType)->first();

        if (! $fallback) {
            throw new InvalidArgumentException('No compatible theme found for selected app type.');
        }

        return $fallback;
    }

    protected function applyThemeSettings(Site $site, Theme $theme, string $path, string $appType, array $data): void
    {
        $settings = $theme->metadata['settings'] ?? [];
        $settings = array_merge($settings, [
            'customer_path' => $path,
            'app_type' => $appType,
            'brand_name' => $data['site_name'] ?? $site->name,
            'installed_by_flow' => 'v16_customer_onboarding',
        ]);

        foreach ($settings as $key => $value) {
            $this->themes->setSiteSetting($site, $theme, (string) $key, $value);
        }
    }

    protected function importRecipeContent(Site $site, Theme $theme, string $appType): void
    {
        $recipe = ThemeAppRecipe::query()
            ->where('theme_id', $theme->id)
            ->where('app_type', $appType)
            ->first();

        if (! $recipe) {
            return;
        }

        $this->demoImporter->importForSite($recipe, $site, [
            'overwrite' => false,
            'pages' => [
                [
                    'title' => 'Home',
                    'slug' => 'home',
                    'excerpt' => 'Starter home page for your Kabeeri app.',
                    'body' => '<p>Your Kabeeri app is active. Start editing your content, services, products, and growth path.</p>',
                ],
                [
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'excerpt' => 'Starter contact page.',
                    'body' => '<p>Capture leads and customer inquiries from this page.</p>',
                ],
            ],
        ]);
    }

    protected function syncProfile(User $user, string $path, string $appType, array $data): UserProfile
    {
        $capabilities = ['customer_owner'];

        foreach (['developer_creator', 'marketer_partner', 'implementation_builder'] as $capability) {
            if ((bool) ($data[$capability] ?? false)) {
                $capabilities[] = $capability;
            }
        }

        if ((bool) ($data['needs_builder_help'] ?? $path === 'needs_builder')) {
            $capabilities[] = 'needs_builder_help';
        }

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id], [
            'visibility' => 'private',
            'metadata' => [],
        ]);
        $profile->forceFill([
            'bio' => $profile->bio,
            'metadata' => array_merge($profile->metadata ?? [], [
                'capabilities' => array_values(array_unique($capabilities)),
                'customer_path' => $path,
                'app_type' => $appType,
                'builder_request_note' => $data['builder_request_note'] ?? null,
                'onboarded_at' => now()->toISOString(),
            ]),
        ])->save();

        return $profile->refresh();
    }

    protected function siteTypeFor(string $appType): string
    {
        return match ($appType) {
            'store' => 'store',
            'landing' => 'landing',
            'portal' => 'portal',
            default => 'website',
        };
    }
}
