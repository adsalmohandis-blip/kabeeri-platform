<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoodsReceiptService
{
    public function createDraft(PurchaseOrder $order): GoodsReceipt
    {
        return GoodsReceipt::query()->create([
            'organization_id' => $order->organization_id,
            'purchase_order_id' => $order->id,
            'warehouse_id' => $order->warehouse_id,
            'receipt_number' => $this->nextReceiptNumber($order),
            'status' => 'draft',
        ]);
    }

    public function receiveItem(GoodsReceipt $receipt, PurchaseOrderItem $orderItem, int $quantity): GoodsReceipt
    {
        if ((int) $orderItem->purchase_order_id !== (int) $receipt->purchase_order_id) {
            throw ValidationException::withMessages([
                'purchase_order_item_id' => 'The purchase order item does not belong to this goods receipt purchase order.',
            ]);
        }

        if ($quantity <= 0 || ($orderItem->received_quantity + $quantity) > $orderItem->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Received quantity must be positive and cannot exceed ordered quantity.',
            ]);
        }

        return DB::transaction(function () use ($receipt, $orderItem, $quantity): GoodsReceipt {
            $movement = null;
            if ($orderItem->inventory_item_id !== null) {
                $movement = app(StockMovementService::class)->record($orderItem->inventoryItem, 'in', $quantity, [
                    'warehouse_id' => $receipt->warehouse_id,
                    'reference_type' => GoodsReceipt::class,
                    'reference_id' => $receipt->id,
                ]);
            }

            $receipt->items()->create([
                'purchase_order_item_id' => $orderItem->id,
                'inventory_item_id' => $orderItem->inventory_item_id,
                'stock_movement_id' => $movement?->id,
                'quantity' => $quantity,
            ]);

            $orderItem->increment('received_quantity', $quantity);

            $receipt->forceFill([
                'status' => 'received',
                'received_at' => now(),
            ])->save();

            $this->refreshPurchaseOrderStatus($receipt->purchaseOrder);

            return $receipt->refresh();
        });
    }

    protected function refreshPurchaseOrderStatus(PurchaseOrder $order): void
    {
        $order->load('items');
        $allReceived = $order->items->every(fn ($item): bool => $item->received_quantity >= $item->quantity);

        $order->forceFill([
            'status' => $allReceived ? 'received' : 'partially_received',
        ])->save();
    }

    protected function nextReceiptNumber(PurchaseOrder $order): string
    {
        $next = GoodsReceipt::query()
            ->where('organization_id', $order->organization_id)
            ->count() + 1;

        return 'GR-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
