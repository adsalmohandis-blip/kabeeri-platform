<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\InvoiceService;
use App\Modules\BusinessOperations\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentsAndReceiptsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_can_be_recorded_for_invoice_with_receipt(): void
    {
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createDraft(Organization::factory()->create());
        $invoice = $invoiceService->addItem($invoice, ['name' => 'Work', 'quantity' => 1, 'unit_price' => 1000]);

        $payment = app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 400]);

        $this->assertNotNull($payment->ulid);
        $this->assertSame('PAY-00001', $payment->payment_number);
        $this->assertSame('RCPT-00001', $payment->receipt_number);
        $this->assertSame('partial', $invoice->refresh()->payment_status);
        $this->assertSame('400.00', $invoice->paid_total);
    }

    public function test_full_payment_marks_invoice_paid(): void
    {
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createDraft(Organization::factory()->create());
        $invoice = $invoiceService->addItem($invoice, ['name' => 'Work', 'quantity' => 1, 'unit_price' => 1000]);

        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 1000]);

        $this->assertSame('paid', $invoice->refresh()->payment_status);
    }

    public function test_payment_amount_must_be_positive(): void
    {
        $this->expectException(ValidationException::class);

        app(PaymentService::class)->recordForInvoice(
            app(InvoiceService::class)->createDraft(Organization::factory()->create()),
            ['amount' => 0]
        );
    }
}
