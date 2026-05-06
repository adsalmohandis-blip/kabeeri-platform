<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\QuotationService;
use App\Modules\BusinessOperations\Services\QuotationToInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConvertQuotationToInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_quotation_can_be_converted_to_invoice(): void
    {
        $quotationService = app(QuotationService::class);
        $quotation = $quotationService->createDraft(Organization::factory()->create());
        $quotationService->addItem($quotation, [
            'name' => 'Implementation',
            'quantity' => 2,
            'unit_price' => 1500,
            'discount_total' => 100,
            'tax_total' => 50,
        ]);
        $accepted = $quotationService->accept($quotationService->issue($quotation->refresh()));

        $invoice = app(QuotationToInvoiceService::class)->convert($accepted);

        $this->assertTrue($invoice->quotation->is($accepted));
        $this->assertSame('2950.00', $invoice->total);
        $this->assertSame(1, $invoice->items()->count());
        $this->assertSame($accepted->quotation_number, $invoice->metadata['converted_from_quotation_number']);
    }

    public function test_draft_quotation_cannot_be_converted(): void
    {
        $this->expectException(ValidationException::class);

        app(QuotationToInvoiceService::class)->convert(
            app(QuotationService::class)->createDraft(Organization::factory()->create())
        );
    }

    public function test_quotation_cannot_be_converted_twice(): void
    {
        $quotationService = app(QuotationService::class);
        $quotation = $quotationService->createDraft(Organization::factory()->create());
        $quotationService->addItem($quotation, ['name' => 'Design', 'quantity' => 1, 'unit_price' => 1000]);
        $accepted = $quotationService->accept($quotationService->issue($quotation->refresh()));

        app(QuotationToInvoiceService::class)->convert($accepted);

        $this->expectException(ValidationException::class);
        app(QuotationToInvoiceService::class)->convert($accepted);
    }
}
