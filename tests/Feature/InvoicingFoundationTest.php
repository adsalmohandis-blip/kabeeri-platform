<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_invoice_can_be_created(): void
    {
        $invoice = app(InvoiceService::class)->createDraft(Organization::factory()->create());

        $this->assertNotNull($invoice->ulid);
        $this->assertSame('INV-00001', $invoice->invoice_number);
        $this->assertSame('draft', $invoice->status);
        $this->assertSame('unpaid', $invoice->payment_status);
    }

    public function test_invoice_items_recalculate_totals(): void
    {
        $service = app(InvoiceService::class);
        $invoice = $service->createDraft(Organization::factory()->create());

        $updated = $service->addItem($invoice, [
            'name' => 'Implementation',
            'quantity' => 2,
            'unit_price' => 1500,
            'discount_total' => 250,
            'tax_total' => 100,
        ]);

        $this->assertSame('3000.00', $updated->subtotal);
        $this->assertSame('250.00', $updated->discount_total);
        $this->assertSame('100.00', $updated->tax_total);
        $this->assertSame('2850.00', $updated->total);
    }

    public function test_invoice_can_be_issued(): void
    {
        $invoice = app(InvoiceService::class)->issue(
            app(InvoiceService::class)->createDraft(Organization::factory()->create())
        );

        $this->assertSame('issued', $invoice->status);
        $this->assertNotNull($invoice->issued_at);
    }
}
