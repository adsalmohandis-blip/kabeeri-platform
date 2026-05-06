<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Invoice;
use App\Models\Organization;

class InvoiceService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes = []): Invoice
    {
        return Invoice::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'invoice_number' => $attributes['invoice_number'] ?? $this->nextInvoiceNumber($organization),
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'currency_code' => $attributes['currency_code'] ?? 'EGP',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addItem(Invoice $invoice, array $attributes): Invoice
    {
        $quantity = (int) ($attributes['quantity'] ?? 1);
        $unitPrice = (float) ($attributes['unit_price'] ?? 0);
        $discount = (float) ($attributes['discount_total'] ?? 0);
        $tax = (float) ($attributes['tax_total'] ?? 0);

        $invoice->items()->create([
            ...$attributes,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'total' => max(0, ($quantity * $unitPrice) - $discount + $tax),
        ]);

        return $this->recalculateTotals($invoice);
    }

    public function issue(Invoice $invoice): Invoice
    {
        $invoice->forceFill([
            'status' => 'issued',
            'issued_at' => now(),
        ])->save();

        return $invoice->refresh();
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        $items = $invoice->items()->get();
        $subtotal = $items->sum(fn ($item): float => (float) $item->quantity * (float) $item->unit_price);
        $discount = $items->sum(fn ($item): float => (float) $item->discount_total);
        $tax = $items->sum(fn ($item): float => (float) $item->tax_total);
        $total = max(0, $subtotal - $discount + $tax);

        $invoice->forceFill([
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'total' => $total,
            'payment_status' => ((float) $invoice->paid_total >= $total && $total > 0) ? 'paid' : 'unpaid',
        ])->save();

        return $invoice->refresh();
    }

    protected function nextInvoiceNumber(Organization $organization): string
    {
        $next = Invoice::query()
            ->where('organization_id', $organization->id)
            ->count() + 1;

        return 'INV-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
