<?php

namespace App\Modules\Core\Services;

use App\Models\Organization;
use App\Models\OrganizationOperatingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrganizationOperatingModeService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createMode(Organization $organization, string $modeKey, string $modeLabel, array $attributes = []): OrganizationOperatingMode
    {
        return OrganizationOperatingMode::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'mode_key' => $modeKey,
            ],
            [
                ...$attributes,
                'mode_label' => $modeLabel,
                'status' => $attributes['status'] ?? 'draft',
                'is_active' => $attributes['is_active'] ?? false,
            ],
        );
    }

    public function activate(OrganizationOperatingMode $mode): OrganizationOperatingMode
    {
        if ($mode->status === 'disabled') {
            throw ValidationException::withMessages([
                'status' => 'Disabled operating modes cannot be activated.',
            ]);
        }

        return DB::transaction(function () use ($mode): OrganizationOperatingMode {
            OrganizationOperatingMode::query()
                ->where('organization_id', $mode->organization_id)
                ->whereKeyNot($mode->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'status' => 'inactive',
                    'deactivated_at' => now(),
                ]);

            $mode->forceFill([
                'status' => 'active',
                'is_active' => true,
                'activated_at' => now(),
                'deactivated_at' => null,
            ])->save();

            return $mode->refresh();
        });
    }

    public function activeFor(Organization $organization): ?OrganizationOperatingMode
    {
        return OrganizationOperatingMode::query()
            ->where('organization_id', $organization->id)
            ->where('is_active', true)
            ->where('status', 'active')
            ->first();
    }
}
