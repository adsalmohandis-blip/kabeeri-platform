<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\QuotationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuotationsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_quotation_can_be_created_for_organization(): void
    {
        $organization = Organization::factory()->create();
        $contact = Contact::factory()->create(['organization_id' => $organization->id]);

        $quotation = app(QuotationService::class)->createDraft($organization, [
            'contact_id' => $contact->id,
            'title' => 'Website build quote',
        ]);

        $this->assertNotNull($quotation->ulid);
        $this->assertSame('QT-00001', $quotation->quotation_number);
        $this->assertSame('draft', $quotation->status);
        $this->assertTrue($quotation->organization->is($organization));
        $this->assertTrue($quotation->contact->is($contact));
    }

    public function test_quotation_items_recalculate_totals(): void
    {
        $quotation = app(QuotationService::class)->createDraft(Organization::factory()->create());
        $service = app(QuotationService::class);

        $service->addItem($quotation, [
            'name' => 'Design',
            'quantity' => 2,
            'unit_price' => 1000,
            'discount_total' => 100,
            'tax_total' => 50,
        ]);
        $updated = $service->addItem($quotation->refresh(), [
            'name' => 'Development',
            'quantity' => 1,
            'unit_price' => 3000,
        ]);

        $this->assertSame('5000.00', $updated->subtotal);
        $this->assertSame('100.00', $updated->discount_total);
        $this->assertSame('50.00', $updated->tax_total);
        $this->assertSame('4950.00', $updated->total);
        $this->assertSame(2, $updated->items()->count());
    }

    public function test_quotation_rejects_other_organization_contact(): void
    {
        $this->expectException(ValidationException::class);

        app(QuotationService::class)->createDraft(Organization::factory()->create(), [
            'contact_id' => Contact::factory()->create()->id,
        ]);
    }
}
