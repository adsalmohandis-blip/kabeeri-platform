<?php

namespace App\Modules\Core\Services;

use App\Models\MarketplaceCatalogItem;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Site;
use Illuminate\Validation\ValidationException;

class PackageInstallationGovernanceService
{
    public function assertInstallable(Package $package, Organization $organization, ?Site $site = null): void
    {
        $this->assertCatalogGovernance($package);
        $this->assertCompatibility($package);
        $this->assertDependencies($package);
    }

    private function assertCatalogGovernance(Package $package): void
    {
        $catalogItem = MarketplaceCatalogItem::query()
            ->where('catalogable_type', $package->getMorphClass())
            ->where('catalogable_id', $package->id)
            ->first();

        if ($catalogItem === null) {
            return;
        }

        if ($catalogItem->listing_status !== 'active' || $catalogItem->governance_status !== 'approved') {
            throw ValidationException::withMessages([
                'package' => 'Package marketplace listing must be approved before installation.',
            ]);
        }
    }

    private function assertCompatibility(Package $package): void
    {
        $compatibility = $package->compatibility ?? [];

        if (($compatibility['v4'] ?? true) === false) {
            throw ValidationException::withMessages([
                'compatibility' => 'Package is not marked compatible with V4.',
            ]);
        }
    }

    private function assertDependencies(Package $package): void
    {
        foreach ($this->dependencyKeys($package->dependencies ?? []) as $dependencyKey) {
            $exists = Package::query()
                ->where('key', $dependencyKey)
                ->where('publisher_type', 'official')
                ->where('status', 'active')
                ->exists();

            if (! $exists) {
                throw ValidationException::withMessages([
                    'dependencies' => 'Package dependency '.$dependencyKey.' is missing or inactive.',
                ]);
            }
        }
    }

    /**
     * @param  array<int, mixed>  $dependencies
     * @return list<string>
     */
    private function dependencyKeys(array $dependencies): array
    {
        $keys = [];

        foreach ($dependencies as $dependency) {
            if (is_string($dependency)) {
                $keys[] = $dependency;

                continue;
            }

            if (is_array($dependency) && is_string($dependency['key'] ?? null)) {
                $keys[] = $dependency['key'];
            }
        }

        return $keys;
    }
}
