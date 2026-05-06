<?php

namespace App\Modules\Platform\Services;

use App\Models\BackofficeWorkspace;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BackofficeWorkspaceService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, ?Company $company = null, ?Site $site = null, array $attributes = []): BackofficeWorkspace
    {
        $this->assertTenantMatch($organization, $company, $site);

        return BackofficeWorkspace::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'company_id' => $company?->id ?? $site?->company_id,
            'site_id' => $site?->id,
            'name' => $attributes['name'] ?? 'Backoffice Workspace',
            'slug' => $attributes['slug'] ?? Str::slug($attributes['name'] ?? 'backoffice-workspace'),
            'workspace_type' => $attributes['workspace_type'] ?? 'operations',
            'access_scope' => $attributes['access_scope'] ?? ($site === null ? 'organization' : 'site'),
            'status' => 'draft',
        ]);
    }

    public function activate(BackofficeWorkspace $workspace): BackofficeWorkspace
    {
        $workspace->forceFill(['status' => 'active'])->save();

        return $workspace->refresh();
    }

    public function archive(BackofficeWorkspace $workspace): BackofficeWorkspace
    {
        $workspace->forceFill(['status' => 'archived'])->save();

        return $workspace->refresh();
    }

    private function assertTenantMatch(Organization $organization, ?Company $company, ?Site $site): void
    {
        if ($company !== null && (int) $company->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'company_id' => 'The selected company does not belong to this organization.',
            ]);
        }

        if ($site !== null && (int) $site->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this organization.',
            ]);
        }

        if ($company !== null && $site !== null && (int) $site->company_id !== (int) $company->id) {
            throw ValidationException::withMessages([
                'site_id' => 'The selected site does not belong to this company.',
            ]);
        }
    }
}
