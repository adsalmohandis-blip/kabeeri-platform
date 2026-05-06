<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Validation\ValidationException;

class QuotationToInvoiceService
{
    public function convert(Quotation $quotation): Invoice
    {
        if ($quotation->status !== 'accepted') {
            throw ValidationException::withMessages([
                'status' => 'Only accepted quotations can be converted to invoices.',
            ]);
        }

        if (Invoice::query()->where('quotation_id', $quotation->id)->exists()) {
            throw ValidationException::withMessages([
                'quotation_id' => 'This quotation already has an invoice.',
            ]);
        }

        $invoice = app(InvoiceService::class)->createDraft($quotation->organization, [
            'company_id' => $quotation->company_id,
            'site_id' => $quotation->site_id,
            'contact_id' => $quotation->contact_id,
            'lead_id' => $quotation->lead_id,
            'quotation_id' => $quotation->id,
            'currency_code' => $quotation->currency_code,
            'notes' => $quotation->notes,
            'metadata' => [
                'converted_from_quotation_id' => $quotation->id,
                'converted_from_quotation_number' => $quotation->quotation_number,
            ],
        ]);

        $quotation->items()
            ->orderBy('sort_order')
            ->get()
            ->each(function ($item) use ($invoice): void {
                app(InvoiceService::class)->addItem($invoice->refresh(), [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount_total' => $item->discount_total,
                    'tax_total' => $item->tax_total,
                    'sort_order' => $item->sort_order,
                    'metadata' => $item->metadata,
                ]);
            });

        return $invoice->refresh();
    }
}
