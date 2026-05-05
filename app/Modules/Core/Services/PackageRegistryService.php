<?php

namespace App\Modules\Core\Services;

use App\Models\Module;
use App\Models\ModuleInstallation;

class PackageRegistryService
{
    /**
     * @param  array<string, mixed>|null  $settings
     */
    public function enableModule(
        int $organizationId,
        int $moduleId,
        ?int $companyId = null,
        ?int $siteId = null,
        ?int $installedBy = null,
        ?array $settings = null,
    ): ModuleInstallation {
        return ModuleInstallation::query()->updateOrCreate(
            [
                'organization_id' => $organizationId,
                'company_id' => $companyId,
                'site_id' => $siteId,
                'module_id' => $moduleId,
            ],
            [
                'status' => 'active',
                'installed_by' => $installedBy,
                'installed_at' => now(),
                'settings' => $settings,
            ],
        );
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public function enableModuleByKey(
        int $organizationId,
        string $moduleKey,
        ?int $companyId = null,
        ?int $siteId = null,
        ?int $installedBy = null,
        ?array $settings = null,
    ): ModuleInstallation {
        $module = Module::query()
            ->where('key', $moduleKey)
            ->firstOrFail();

        return $this->enableModule(
            organizationId: $organizationId,
            moduleId: $module->id,
            companyId: $companyId,
            siteId: $siteId,
            installedBy: $installedBy,
            settings: $settings,
        );
    }

    public function isEnabled(
        int $organizationId,
        string $moduleKey,
        ?int $companyId = null,
        ?int $siteId = null,
    ): bool {
        return ModuleInstallation::query()
            ->where('organization_id', $organizationId)
            ->where('company_id', $companyId)
            ->where('site_id', $siteId)
            ->where('status', 'active')
            ->whereHas('module', function ($query) use ($moduleKey): void {
                $query->where('key', $moduleKey)->where('status', 'active');
            })
            ->exists();
    }
}
