<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function recordForInvoice(Invoice $invoice, array $attributes): Payment
    {
        $amount = (float) ($attributes['amount'] ?? 0);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payment amount must be greater than zero.',
            ]);
        }

        $payment = Payment::query()->create([
            ...$attributes,
            'organization_id' => $invoice->organization_id,
            'invoice_id' => $invoice->id,
            'payment_number' => $attributes['payment_number'] ?? $this->nextPaymentNumber($invoice),
            'receipt_number' => $attributes['receipt_number'] ?? $this->nextReceiptNumber($invoice),
            'amount' => $amount,
            'currency_code' => $attributes['currency_code'] ?? $invoice->currency_code,
            'status' => 'recorded',
            'paid_at' => $attributes['paid_at'] ?? now(),
        ]);

        $this->refreshInvoicePaymentStatus($invoice);

        return $payment->refresh();
    }

    public function refreshInvoicePaymentStatus(Invoice $invoice): Invoice
    {
        $paid = (float) $invoice->payments()
            ->where('status', 'recorded')
            ->sum('amount');
        $total = (float) $invoice->total;

        $invoice->forceFill([
            'paid_total' => $paid,
            'payment_status' => match (true) {
                $paid <= 0 => 'unpaid',
                $paid < $total => 'partial',
                default => 'paid',
            },
        ])->save();

        return $invoice->refresh();
    }

    protected function nextPaymentNumber(Invoice $invoice): string
    {
        $next = Payment::query()
            ->where('organization_id', $invoice->organization_id)
            ->count() + 1;

        return 'PAY-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    protected function nextReceiptNumber(Invoice $invoice): string
    {
        $next = Payment::query()
            ->where('organization_id', $invoice->organization_id)
            ->count() + 1;

        return 'RCPT-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
