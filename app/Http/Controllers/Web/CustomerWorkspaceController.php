<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Modules\Core\Services\CustomerWorkspaceService;
use App\Support\Localization\KabeeriLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerWorkspaceController extends Controller
{
    public function onboarding(Request $request, CustomerWorkspaceService $workspace): View|RedirectResponse
    {
        if ($request->user()?->ownedOrganizations()->exists()) {
            return redirect()->route('customer.workspace');
        }

        $selectedPath = (string) $request->query('path', 'business_owner');
        $paths = config('kabeeri_customer.audience_paths', []);
        $pathConfig = $paths[$selectedPath] ?? ($paths['business_owner'] ?? []);
        $selectedAppType = (string) $request->query('app_type', $pathConfig['default_app_type'] ?? 'website');

        return view('customer.v16-onboarding', [
            'paths' => $paths,
            'appTypes' => config('kabeeri_customer.app_types', []),
            'themes' => $workspace->starterThemes($selectedAppType),
            'capabilities' => config('kabeeri_customer.capabilities', []),
            'selectedPath' => $selectedPath,
            'selectedAppType' => $selectedAppType,
        ]);
    }

    public function storeOnboarding(Request $request, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $paths = array_keys(config('kabeeri_customer.audience_paths', []));
        $appTypes = array_keys(config('kabeeri_customer.app_types', []));

        $validated = $request->validate([
            'customer_path' => ['required', 'string', Rule::in($paths)],
            'organization_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'app_type' => ['required', 'string', Rule::in($appTypes)],
            'theme_slug' => ['required', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'developer_creator' => ['nullable', 'boolean'],
            'marketer_partner' => ['nullable', 'boolean'],
            'implementation_builder' => ['nullable', 'boolean'],
            'needs_builder_help' => ['nullable', 'boolean'],
            'builder_request_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $workspace->provision($request->user(), $validated);

        return redirect()
            ->route('customer.workspace')
            ->with('status', __('kabeeri.ui.workspace_created'));
    }

    public function dashboard(Request $request, CustomerWorkspaceService $workspace): View
    {
        return view('customer.v16-dashboard', [
            'dashboard' => $workspace->dashboard($request->user()),
        ]);
    }

    public function apps(Request $request, CustomerWorkspaceService $workspace): View
    {
        return view('customer.v16-apps-index', [
            'dashboard' => $workspace->dashboard($request->user()),
            'sites' => $workspace->activeSites($request->user()),
        ]);
    }

    public function createApp(Request $request, CustomerWorkspaceService $workspace): View|RedirectResponse
    {
        $dashboard = $workspace->dashboard($request->user());

        if ($dashboard['organizations']->isEmpty()) {
            return redirect()->route('customer.onboarding');
        }

        $selectedAppType = (string) $request->query('app_type', 'website');

        return view('customer.v16-app-form', [
            'mode' => 'create',
            'dashboard' => $dashboard,
            'site' => null,
            'appTypes' => config('kabeeri_customer.app_types', []),
            'themes' => $workspace->starterThemes($selectedAppType),
            'selectedAppType' => $selectedAppType,
        ]);
    }

    public function storeApp(Request $request, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $appTypes = array_keys(config('kabeeri_customer.app_types', []));

        $validated = $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'site_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9][A-Za-z0-9_-]*$/'],
            'app_type' => ['required', 'string', Rule::in($appTypes)],
            'theme_slug' => ['required', 'string', 'max:255'],
            'public_language' => ['nullable', 'string', Rule::in(KabeeriLocale::codes())],
            'public_theme_mode' => ['nullable', 'string', Rule::in(array_keys(config('kabeeri_ui_preferences.themes', [])))],
            'public_font' => ['nullable', 'string', Rule::in(array_keys(config('kabeeri_ui_preferences.fonts', [])))],
            'language' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $site = $workspace->createApp($request->user(), $validated);

        return redirect()
            ->route('customer.apps.show', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.app_created'));
    }

    public function editApp(Request $request, string $username, CustomerWorkspaceService $workspace): View
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $selectedAppType = (string) ($site->metadata['v16_app_type'] ?? $site->site_type);

        return view('customer.v16-app-form', [
            'mode' => 'edit',
            'dashboard' => $workspace->dashboard($request->user()),
            'site' => $site,
            'appTypes' => config('kabeeri_customer.app_types', []),
            'themes' => $workspace->starterThemes($selectedAppType),
            'selectedAppType' => $selectedAppType,
        ]);
    }

    public function updateApp(Request $request, string $username, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $appTypes = array_keys(config('kabeeri_customer.app_types', []));

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9][A-Za-z0-9_-]*$/'],
            'app_type' => ['required', 'string', Rule::in($appTypes)],
            'status' => ['required', 'string', Rule::in(['active', 'paused'])],
            'public_language' => ['nullable', 'string', Rule::in(KabeeriLocale::codes())],
            'public_theme_mode' => ['nullable', 'string', Rule::in(array_keys(config('kabeeri_ui_preferences.themes', [])))],
            'public_font' => ['nullable', 'string', Rule::in(array_keys(config('kabeeri_ui_preferences.fonts', [])))],
            'language' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $site = $workspace->updateApp($request->user(), $site, $validated);

        return redirect()
            ->route('customer.apps.show', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.app_updated'));
    }

    public function trashApp(Request $request, string $username, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);

        $validated = $request->validate([
            'retention_days' => ['required', 'integer', Rule::in([30, 60, 90])],
        ]);

        $workspace->trashApp($request->user(), $site, (int) $validated['retention_days']);

        return redirect()
            ->route('customer.apps.trash')
            ->with('status', __('kabeeri.ui.app_moved_to_trash'));
    }

    public function trash(Request $request, CustomerWorkspaceService $workspace): View
    {
        return view('customer.v16-app-trash', [
            'dashboard' => $workspace->dashboard($request->user()),
            'trashedSites' => $workspace->trashedSites($request->user()),
        ]);
    }

    public function restoreApp(Request $request, string $username, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace, true);
        $workspace->restoreApp($request->user(), $site);

        return redirect()
            ->route('customer.apps.index')
            ->with('status', __('kabeeri.ui.app_restored'));
    }

    public function scheduleTrashPurge(Request $request, string $username, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace, true);
        $validated = $request->validate([
            'retention_days' => ['required', 'integer', Rule::in([30, 60, 90])],
        ]);

        $workspace->scheduleTrashPurge($request->user(), $site, (int) $validated['retention_days']);

        return redirect()
            ->route('customer.apps.trash')
            ->with('status', __('kabeeri.ui.trash_schedule_updated'));
    }

    public function showSite(Request $request, string $username, CustomerWorkspaceService $workspace): View
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);

        return view('customer.v16-site', [
            'site' => $site->load(['theme', 'themeSettings', 'contentEntries']),
            'themes' => $workspace->starterThemes($site->metadata['v16_app_type'] ?? $site->site_type),
        ]);
    }

    public function themes(Request $request, string $username, CustomerWorkspaceService $workspace): View
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $appType = (string) ($site->metadata['v16_app_type'] ?? $site->site_type);

        return view('customer.v16-app-themes', [
            'dashboard' => $workspace->dashboard($request->user()),
            'site' => $site,
            'themes' => $workspace->starterThemes($appType),
        ]);
    }

    public function switchTheme(Request $request, string $username, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $validated = $request->validate([
            'theme_slug' => ['required', 'string', 'max:255'],
        ]);

        $workspace->switchTheme($request->user(), $site, $validated['theme_slug']);

        return redirect()
            ->route('customer.apps.themes', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.theme_updated'));
    }

    public function plugins(Request $request, string $username, CustomerWorkspaceService $workspace): View
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);

        return view('customer.v16-app-plugins', [
            'dashboard' => $workspace->dashboard($request->user()),
            'site' => $site->load(['installedPackages.package']),
            'plugins' => $workspace->pluginCatalog(),
        ]);
    }

    public function installPlugin(Request $request, string $username, string $package, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $workspace->installPlugin($request->user(), $site, $package);

        return redirect()
            ->route('customer.apps.plugins', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.plugin_installed'));
    }

    public function deactivatePlugin(Request $request, string $username, string $package, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $workspace->setPluginStatus($request->user(), $site, $package, 'inactive');

        return redirect()
            ->route('customer.apps.plugins', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.plugin_deactivated'));
    }

    public function activatePlugin(Request $request, string $username, string $package, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $site = $this->ownedSiteOrFail($request, $username, $workspace);
        $workspace->setPluginStatus($request->user(), $site, $package, 'active');

        return redirect()
            ->route('customer.apps.plugins', ['username' => $site->username])
            ->with('status', __('kabeeri.ui.plugin_activated'));
    }

    public function updateCapabilities(Request $request, CustomerWorkspaceService $workspace): RedirectResponse
    {
        $validated = $request->validate([
            'capabilities' => ['nullable', 'array'],
            'capabilities.*' => ['string'],
            'capability_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $workspace->updateCapabilities(
            $request->user(),
            $validated['capabilities'] ?? [],
            $validated['capability_note'] ?? null,
        );

        return redirect()
            ->route('customer.workspace')
            ->with('status', __('kabeeri.ui.profile_updated'));
    }

    private function ownedSiteOrFail(Request $request, string $username, CustomerWorkspaceService $workspace, bool $withTrashed = false): Site
    {
        $site = $workspace->ownedSite($request->user(), $username, $withTrashed);

        abort_if($site === null && Site::withTrashed()->where('slug', $username)->exists(), 403);
        abort_if($site === null, 404);

        return $site;
    }
}
