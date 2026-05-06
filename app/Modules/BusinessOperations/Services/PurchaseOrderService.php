<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Validation\ValidationException;

class PurchaseOrderService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Supplier $supplier, array $attributes = []): PurchaseOrder
    {
        if (($attributes['warehouse_id'] ?? null) !== null) {
            $warehouse = Warehouse::query()->findOrFail($attributes['warehouse_id']);

            if ((int) $warehouse->organization_id !== (int) $supplier->organization_id) {
                throw ValidationException::withMessages([
                    'warehouse_id' => 'The selected warehouse does not belong to this supplier organization.',
                ]);
            }
        }

        return PurchaseOrder::query()->create([
            ...$attributes,
            'organization_id' => $supplier->organization_id,
            'supplier_id' => $supplier->id,
            'purchase_order_number' => $attributes['purchase_order_number'] ?? $this->nextPurchaseOrderNumber($supplier),
            'status' => 'draft',
            'currency_code' => $attributes['currency_code'] ?? 'EGP',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addItem(PurchaseOrder $order, array $attributes): PurchaseOrder
    {
        if (($attributes['inventory_item_id'] ?? null) !== null) {
            $item = InventoryItem::query()->findOrFail($attributes['inventory_item_id']);

            if ((int) $item->organization_id !== (int) $order->organization_id) {
                throw ValidationException::withMessages([
                    'inventory_item_id' => 'The selected inventory item does not belong to this purchase order organization.',
                ]);
            }
        }

        $quantity = (int) ($attributes['quantity'] ?? 1);
        $unitCost = (float) ($attributes['unit_cost'] ?? 0);
        $tax = (float) ($attributes['tax_total'] ?? 0);

        $order->items()->create([
            ...$attributes,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'tax_total' => $tax,
            'total' => ($quantity * $unitCost) + $tax,
        ]);

        return $this->recalculateTotals($order);
    }

    public function order(PurchaseOrder $order): PurchaseOrder
    {
        $order->forceFill([
            'status' => 'ordered',
            'ordered_at' => now(),
        ])->save();

        return $order->refresh();
    }

    public function recalculateTotals(PurchaseOrder $order): PurchaseOrder
    {
        $items = $order->items()->get();
        $subtotal = $items->sum(fn ($item): float => (float) $item->quantity * (float) $item->unit_cost);
        $tax = $items->sum(fn ($item): float => (float) $item->tax_total);

        $order->forceFill([
            'subtotal' => $subtotal,
            'tax_total' => $tax,
            'total' => $subtotal + $tax,
        ])->save();

        return $order->refresh();
    }

    protected function nextPurchaseOrderNumber(Supplier $supplier): string
    {
        $next = PurchaseOrder::query()
            ->where('organization_id', $supplier->organization_id)
            ->count() + 1;

        return 'PO-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
