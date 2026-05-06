<?php

namespace Tests\Feature;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentSalesInvoicingResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_sales_invoicing_resource_indexes(): void
    {
        $owner = User::factory()->create();
        Organization::factory()->create(['owner_user_id' => $owner->id]);

        $this->actingAs($owner);

        $this->get(route('filament.admin.resources.quotations.index'))->assertOk();
        $this->get(route('filament.admin.resources.invoices.index'))->assertOk();
        $this->get(route('filament.admin.resources.payments.index'))->assertOk();
    }

    public function test_sales_invoicing_resource_queries_are_tenant_scoped(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $visibleQuotation = Quotation::factory()->create(['organization_id' => $organization->id]);
        $hiddenQuotation = Quotation::factory()->create();
        $visibleInvoice = Invoice::factory()->create(['organization_id' => $organization->id]);
        $hiddenInvoice = Invoice::factory()->create();
        $visiblePayment = Payment::factory()->create(['organization_id' => $organization->id]);
        $hiddenPayment = Payment::factory()->create();

        $this->actingAs($owner);

        $this->assertTrue(QuotationResource::getEloquentQuery()->whereKey($visibleQuotation->id)->exists());
        $this->assertFalse(QuotationResource::getEloquentQuery()->whereKey($hiddenQuotation->id)->exists());
        $this->assertTrue(InvoiceResource::getEloquentQuery()->whereKey($visibleInvoice->id)->exists());
        $this->assertFalse(InvoiceResource::getEloquentQuery()->whereKey($hiddenInvoice->id)->exists());
        $this->assertTrue(PaymentResource::getEloquentQuery()->whereKey($visiblePayment->id)->exists());
        $this->assertFalse(PaymentResource::getEloquentQuery()->whereKey($hiddenPayment->id)->exists());
    }
}
