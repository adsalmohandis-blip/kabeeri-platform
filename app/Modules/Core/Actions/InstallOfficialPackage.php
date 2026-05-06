<?php

namespace App\Modules\Core\Actions;

use App\Models\InstalledPackage;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\PackageInstallationGovernanceService;
use App\Modules\Core\Services\PermissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class InstallOfficialPackage
{
    public function __construct(
        protected PermissionService $permissionService,
        protected ActivityLogger $activityLogger,
        protected PackageInstallationGovernanceService $governanceService,
    ) {}

    public function __invoke(User $actor, Package $package, Organization $organization, ?Site $site = null): InstalledPackage
    {
        if (! $this->permissionService->hasOrganizationPermission($actor, $organization->id, 'package.install')) {
            throw new AuthorizationException('You are not allowed to install packages.');
        }

        if ($package->publisher_type !== 'official' || $package->status !== 'active') {
            throw ValidationException::withMessages([
                'package' => 'Only active official packages can be installed.',
            ]);
        }

        if ($site !== null && $site->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'site' => 'The app does not belong to the organization.',
            ]);
        }

        $this->governanceService->assertInstallable($package, $organization, $site);

        $installation = InstalledPackage::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site?->id,
                'package_id' => $package->id,
            ],
            [
                'status' => 'active',
                'installed_by' => $actor->id,
                'installed_at' => now(),
                'settings' => [
                    'dependencies' => $package->dependencies ?? [],
                    'compatibility' => $package->compatibility ?? [],
                ],
            ],
        );

        $this->activityLogger->log(
            action: 'package.installed',
            organizationId: $organization->id,
            siteId: $site?->id,
            actorUserId: $actor->id,
            description: 'Official package installed',
            subject: $installation,
        );

        return $installation->refresh();
    }
}
