<?php

namespace App\Modules\Core\Services;

use App\Models\InstalledPackage;
use App\Models\Organization;
use App\Models\Package;
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
                'seed_page_body' => 'Welcome to your kabeeri app. This starter page was created by V16 onboarding.',
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
                    'public_language' => (string) ($data['language'] ?? $site->language ?? 'ar'),
                    'public_theme_mode' => (string) ($data['public_theme_mode'] ?? config('kabeeri_ui_preferences.default_theme', 'light')),
                    'public_font' => (string) ($data['public_font'] ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic')),
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
            ->with(['sites.theme', 'sites.installedPackages.package', 'companies'])
            ->latest('id')
            ->get();
        $activeOrganization = $organizations->first();
        $sites = $activeOrganization?->sites ?? collect();
        $ownedOrganizationIds = $organizations->pluck('id');
        $trashedSites = Site::onlyTrashed()
            ->whereIn('organization_id', $ownedOrganizationIds)
            ->with(['theme', 'installedPackages.package'])
            ->latest('deleted_at')
            ->get();
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
            'trashed_sites' => $trashedSites,
            'cards' => config('kabeeri_customer.dashboard_cards', []),
            'capabilities' => config('kabeeri_customer.capabilities', []),
        ];
    }

    /**
     * @return Collection<int, Site>
     */
    public function activeSites(User $user): Collection
    {
        return Site::query()
            ->whereIn('organization_id', $this->ownedOrganizationIds($user))
            ->with(['theme', 'installedPackages.package'])
            ->latest('id')
            ->get();
    }

    /**
     * @return Collection<int, Site>
     */
    public function trashedSites(User $user): Collection
    {
        return Site::onlyTrashed()
            ->whereIn('organization_id', $this->ownedOrganizationIds($user))
            ->with(['theme', 'installedPackages.package'])
            ->latest('deleted_at')
            ->get();
    }

    public function ownedSite(User $user, string $username, bool $withTrashed = false): ?Site
    {
        $query = Site::query()
            ->whereIn('organization_id', $this->ownedOrganizationIds($user))
            ->where('slug', $username);

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->with(['theme', 'themeSettings', 'contentEntries', 'installedPackages.package'])->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createApp(User $user, array $data): Site
    {
        $this->ensureStarterThemes();

        $organization = $this->organizationForApp($user, $data['organization_id'] ?? null);
        $appType = (string) ($data['app_type'] ?? 'website');
        $theme = $this->resolveTheme((string) ($data['theme_slug'] ?? ''), $appType);
        $name = trim((string) ($data['site_name'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('site_name is required.');
        }

        return DB::transaction(function () use ($user, $data, $organization, $appType, $theme, $name): Site {
            $publicLanguage = (string) ($data['public_language'] ?? $data['language'] ?? $organization->locale ?? 'ar');
            $publicThemeMode = (string) ($data['public_theme_mode'] ?? config('kabeeri_ui_preferences.default_theme', 'light'));
            $publicFont = (string) ($data['public_font'] ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic'));

            $site = Site::query()->create([
                'organization_id' => $organization->id,
                'company_id' => $organization->companies()->latest('id')->value('id'),
                'name' => $name,
                'slug' => $this->uniqueUsername((string) ($data['username'] ?? $name)),
                'site_type' => $this->siteTypeFor($appType),
                'status' => 'active',
                'language' => $publicLanguage,
                'timezone' => (string) ($data['timezone'] ?? $organization->timezone ?? 'Africa/Cairo'),
                'settings' => ['app_label' => $name],
                'metadata' => [
                    'v16_customer_path' => $organization->metadata['v16_customer_path'] ?? 'business_owner',
                    'v16_app_type' => $appType,
                    'created_from' => 'customer_dashboard',
                    'public_language' => $publicLanguage,
                    'public_theme_mode' => $publicThemeMode,
                    'public_font' => $publicFont,
                ],
                'created_by' => $user->id,
            ]);

            $this->themes->assignToSite($site, $theme);
            $this->applyThemeSettings($site->refresh(), $theme, (string) ($site->metadata['v16_customer_path'] ?? 'business_owner'), $appType, [
                'site_name' => $name,
            ]);
            $this->importRecipeContent($site->refresh(), $theme, $appType);

            $site->forceFill([
                'metadata' => array_merge($site->metadata ?? [], [
                    'theme_installed_at' => now()->toISOString(),
                    'theme_install_status' => 'active',
                    'v16_theme_slug' => $theme->slug,
                    'public_language' => $publicLanguage,
                    'public_theme_mode' => $publicThemeMode,
                    'public_font' => $publicFont,
                ]),
            ])->save();

            return $site->refresh()->load(['theme', 'themeSettings', 'contentEntries']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateApp(User $user, Site $site, array $data): Site
    {
        abort_unless($this->userOwnsSite($user, $site), 403);

        $metadata = $site->metadata ?? [];
        $appType = (string) ($data['app_type'] ?? $metadata['v16_app_type'] ?? $site->site_type);
        $publicLanguage = (string) ($data['public_language'] ?? $metadata['public_language'] ?? $site->language ?? 'ar');
        $publicThemeMode = (string) ($data['public_theme_mode'] ?? $metadata['public_theme_mode'] ?? config('kabeeri_ui_preferences.default_theme', 'light'));
        $publicFont = (string) ($data['public_font'] ?? $metadata['public_font'] ?? config('kabeeri_ui_preferences.default_font', 'ibm-plex-sans-arabic'));

        $site->forceFill([
            'name' => trim((string) ($data['site_name'] ?? $site->name)),
            'site_type' => $this->siteTypeFor($appType),
            'language' => $publicLanguage,
            'timezone' => (string) ($data['timezone'] ?? $site->timezone),
            'status' => (string) ($data['status'] ?? $site->status),
            'settings' => array_merge($site->settings ?? [], [
                'app_label' => trim((string) ($data['site_name'] ?? $site->name)),
            ]),
            'metadata' => array_merge($metadata, [
                'v16_app_type' => $appType,
                'updated_from' => 'customer_dashboard',
                'customer_updated_at' => now()->toISOString(),
                'public_language' => $publicLanguage,
                'public_theme_mode' => $publicThemeMode,
                'public_font' => $publicFont,
            ]),
        ])->save();

        return $site->refresh()->load(['theme', 'themeSettings', 'contentEntries', 'installedPackages.package']);
    }

    public function trashApp(User $user, Site $site, int $retentionDays): Site
    {
        abort_unless($this->userOwnsSite($user, $site), 403);

        $days = $this->normalizeRetentionDays($retentionDays);
        $site->forceFill([
            'status' => 'trashed',
            'metadata' => array_merge($site->metadata ?? [], [
                'trash_retention_days' => $days,
                'trash_scheduled_delete_at' => now()->addDays($days)->toISOString(),
                'trashed_by_user_id' => $user->id,
            ]),
        ])->save();
        $site->delete();

        return $site->refresh();
    }

    public function restoreApp(User $user, Site $site): Site
    {
        abort_unless($this->userOwnsSite($user, $site), 403);
        abort_unless($site->trashed(), 404);

        $site->restore();
        $site->forceFill([
            'status' => 'active',
            'metadata' => array_merge($site->metadata ?? [], [
                'restored_at' => now()->toISOString(),
                'trash_scheduled_delete_at' => null,
            ]),
        ])->save();

        return $site->refresh();
    }

    public function scheduleTrashPurge(User $user, Site $site, int $retentionDays): Site
    {
        abort_unless($this->userOwnsSite($user, $site), 403);
        abort_unless($site->trashed(), 404);

        $days = $this->normalizeRetentionDays($retentionDays);
        $site->forceFill([
            'metadata' => array_merge($site->metadata ?? [], [
                'trash_retention_days' => $days,
                'trash_scheduled_delete_at' => now()->addDays($days)->toISOString(),
                'trash_schedule_updated_at' => now()->toISOString(),
            ]),
        ])->save();

        return $site->refresh();
    }

    public function switchTheme(User $user, Site $site, string $themeSlug): Site
    {
        abort_unless($this->userOwnsSite($user, $site), 403);

        $appType = (string) ($site->metadata['v16_app_type'] ?? $site->site_type);
        $theme = $this->resolveTheme($themeSlug, $appType);
        $previousTheme = $site->theme?->slug;

        $this->themes->assignToSite($site, $theme);
        $this->applyThemeSettings($site->refresh(), $theme, (string) ($site->metadata['v16_customer_path'] ?? 'business_owner'), $appType, [
            'site_name' => $site->name,
        ]);

        $site->forceFill([
            'metadata' => array_merge($site->metadata ?? [], [
                'previous_theme_slug' => $previousTheme,
                'v16_theme_slug' => $theme->slug,
                'theme_installed_at' => now()->toISOString(),
                'theme_install_status' => 'active',
            ]),
        ])->save();

        return $site->refresh()->load(['theme', 'themeSettings']);
    }

    /**
     * @return Collection<int, Package>
     */
    public function pluginCatalog(): Collection
    {
        $this->ensureCustomerPlugins();

        return Package::query()
            ->where('status', 'active')
            ->whereIn('package_type', ['plugin', 'plugin bundle', 'extension', 'AI skill'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function installPlugin(User $user, Site $site, string $packageSlug): InstalledPackage
    {
        abort_unless($this->userOwnsSite($user, $site), 403);

        $package = $this->pluginCatalog()->firstWhere('slug', $packageSlug);

        if (! $package) {
            throw new InvalidArgumentException('Invalid plugin package.');
        }

        return InstalledPackage::query()->updateOrCreate(
            [
                'organization_id' => $site->organization_id,
                'site_id' => $site->id,
                'package_id' => $package->id,
            ],
            [
                'status' => 'active',
                'installed_by' => $user->id,
                'installed_at' => now(),
                'settings' => ['source' => 'customer_dashboard', 'activated_at' => now()->toISOString()],
            ],
        )->refresh()->load('package');
    }

    public function setPluginStatus(User $user, Site $site, string $packageSlug, string $status): InstalledPackage
    {
        abort_unless($this->userOwnsSite($user, $site), 403);

        $installed = $site->installedPackages()
            ->whereHas('package', fn ($query) => $query->where('slug', $packageSlug))
            ->first();

        if (! $installed && $status === 'active') {
            return $this->installPlugin($user, $site, $packageSlug);
        }

        abort_unless($installed !== null, 404);

        $installed->forceFill([
            'status' => $status,
            'settings' => array_merge($installed->settings ?? [], [
                $status === 'active' ? 'reactivated_at' : 'deactivated_at' => now()->toISOString(),
            ]),
        ])->save();

        return $installed->refresh()->load('package');
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
                'publisher' => $theme['publisher'] ?? 'kabeeri Official',
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

    public function ensureCustomerPlugins(): void
    {
        foreach (config('kabeeri_marketplace.plugins', []) as $plugin) {
            Package::query()->updateOrCreate(
                ['slug' => $plugin['slug']],
                [
                    'key' => $plugin['slug'],
                    'name' => $plugin['name'],
                    'package_type' => $plugin['package_type'],
                    'publisher_type' => $plugin['publisher'] ?? 'official',
                    'status' => 'active',
                    'short_description' => $plugin['support_policy'] ?? null,
                    'description' => $plugin['support_policy'] ?? null,
                    'category' => $plugin['category'] ?? 'general',
                    'permissions' => $plugin['permissions'] ?? [],
                    'dependencies' => $plugin['dependencies'] ?? [],
                    'compatibility' => $plugin['compatibility'] ?? [],
                    'metadata' => [
                        'risk' => $plugin['risk'] ?? 'low',
                        'price_type' => $plugin['price_type'] ?? 'included',
                        'source' => 'v12_marketplace_defaults',
                    ],
                ],
            );
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
                    'excerpt' => 'Starter home page for your kabeeri app.',
                    'body' => '<p>Your kabeeri app is active. Start editing your content, services, products, and growth path.</p>',
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

    /**
     * @return Collection<int, int>
     */
    protected function ownedOrganizationIds(User $user): Collection
    {
        return $user->ownedOrganizations()->pluck('organizations.id');
    }

    protected function organizationForApp(User $user, mixed $organizationId = null): Organization
    {
        $query = $user->ownedOrganizations()->with('companies');

        if ($organizationId) {
            $query->where('organizations.id', $organizationId);
        }

        $organization = $query->first();

        if (! $organization) {
            throw new InvalidArgumentException('Create a workspace before adding apps.');
        }

        return $organization;
    }

    protected function uniqueUsername(string $name, ?Site $ignore = null): string
    {
        $base = Site::normalizeUsername($name);
        $candidate = $base;
        $suffix = 2;

        while (
            Site::withTrashed()
                ->where('slug', $candidate)
                ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
                ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    protected function userOwnsSite(User $user, Site $site): bool
    {
        return $this->ownedOrganizationIds($user)->contains($site->organization_id);
    }

    protected function normalizeRetentionDays(int $days): int
    {
        return in_array($days, [30, 60, 90], true) ? $days : 30;
    }
}
