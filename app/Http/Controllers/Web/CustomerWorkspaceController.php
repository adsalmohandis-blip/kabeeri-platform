<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Modules\Core\Services\CustomerWorkspaceService;
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
            ->with('status', 'Your workspace, app, and theme were created successfully.');
    }

    public function dashboard(Request $request, CustomerWorkspaceService $workspace): View
    {
        return view('customer.v16-dashboard', [
            'dashboard' => $workspace->dashboard($request->user()),
        ]);
    }

    public function showSite(Request $request, string $username, CustomerWorkspaceService $workspace): View
    {
        $user = $request->user();
        $ownedOrganizationIds = $user?->ownedOrganizations()->pluck('organizations.id') ?? collect();
        $site = Site::query()
            ->whereIn('organization_id', $ownedOrganizationIds)
            ->where('slug', $username)
            ->first();

        abort_if($site === null && Site::query()->where('slug', $username)->exists(), 403);
        abort_if($site === null, 404);

        return view('customer.v16-site', [
            'site' => $site->load(['theme', 'themeSettings', 'contentEntries']),
            'themes' => $workspace->starterThemes($site->metadata['v16_app_type'] ?? $site->site_type),
        ]);
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
            ->with('status', 'Your profile capabilities were updated.');
    }
}
