<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function record(InventoryItem $item, string $movementType, float $quantity, array $attributes = []): StockMovement
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock movement quantity must be greater than zero.',
            ]);
        }

        $warehouse = null;
        if (($attributes['warehouse_id'] ?? null) !== null) {
            $warehouse = Warehouse::query()->findOrFail($attributes['warehouse_id']);

            if ((int) $warehouse->organization_id !== (int) $item->organization_id) {
                throw ValidationException::withMessages([
                    'warehouse_id' => 'The selected warehouse does not belong to this inventory item organization.',
                ]);
            }
        }

        return DB::transaction(function () use ($item, $movementType, $quantity, $attributes, $warehouse): StockMovement {
            $item->refresh();
            $before = (float) $item->current_quantity;
            $signedQuantity = in_array($movementType, ['out', 'sale', 'consume'], true) ? -$quantity : $quantity;
            $after = $before + $signedQuantity;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock movement cannot reduce quantity below zero.',
                ]);
            }

            $item->forceFill(['current_quantity' => $after])->save();

            return StockMovement::query()->create([
                ...$attributes,
                'organization_id' => $item->organization_id,
                'warehouse_id' => $warehouse?->id,
                'inventory_item_id' => $item->id,
                'movement_type' => $movementType,
                'quantity' => $signedQuantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
            ]);
        });
    }
}
