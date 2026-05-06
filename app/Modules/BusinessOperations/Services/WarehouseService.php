<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Organization;
use App\Models\Warehouse;

class WarehouseService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): Warehouse
    {
        $warehouse = Warehouse::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
            'is_default' => $attributes['is_default'] ?? false,
        ]);

        if ($warehouse->is_default) {
            $this->makeDefault($warehouse);
        }

        return $warehouse->refresh();
    }

    public function makeDefault(Warehouse $warehouse): Warehouse
    {
        Warehouse::query()
            ->where('organization_id', $warehouse->organization_id)
            ->whereKeyNot($warehouse->id)
            ->update(['is_default' => false]);

        $warehouse->forceFill(['is_default' => true])->save();

        return $warehouse->refresh();
    }
}
