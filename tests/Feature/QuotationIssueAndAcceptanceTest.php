<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\QuotationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuotationIssueAndAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_quotation_can_be_issued_and_accepted(): void
    {
        $service = app(QuotationService::class);
        $quotation = $service->createDraft(Organization::factory()->create());
        $service->addItem($quotation, ['name' => 'Design', 'quantity' => 1, 'unit_price' => 1000]);

        $issued = $service->issue($quotation->refresh());
        $this->assertSame('issued', $issued->status);
        $this->assertNotNull($issued->issued_at);

        $accepted = $service->accept($issued);
        $this->assertSame('accepted', $accepted->status);
        $this->assertNotNull($accepted->accepted_at);
        $this->assertNull($accepted->declined_at);
    }

    public function test_quotation_cannot_be_issued_without_items(): void
    {
        $this->expectException(ValidationException::class);

        app(QuotationService::class)->issue(
            app(QuotationService::class)->createDraft(Organization::factory()->create())
        );
    }

    public function test_issued_quotation_can_be_declined(): void
    {
        $service = app(QuotationService::class);
        $quotation = $service->createDraft(Organization::factory()->create());
        $service->addItem($quotation, ['name' => 'Support', 'quantity' => 1, 'unit_price' => 500]);

        $declined = $service->decline($service->issue($quotation->refresh()), 'Too expensive');

        $this->assertSame('declined', $declined->status);
        $this->assertSame('Too expensive', $declined->decline_reason);
        $this->assertNotNull($declined->declined_at);
    }
}
