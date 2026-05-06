<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryItemService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): InventoryItem
    {
        if (($attributes['product_id'] ?? null) !== null) {
            $product = Product::query()->findOrFail($attributes['product_id']);

            if ((int) $product->organization_id !== (int) $organization->id) {
                throw ValidationException::withMessages([
                    'product_id' => 'The selected product does not belong to this organization.',
                ]);
            }
        }

        return InventoryItem::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
            'item_type' => $attributes['item_type'] ?? 'stocked',
            'unit_of_measure' => $attributes['unit_of_measure'] ?? 'pcs',
            'current_quantity' => $attributes['current_quantity'] ?? 0,
        ]);
    }
}
