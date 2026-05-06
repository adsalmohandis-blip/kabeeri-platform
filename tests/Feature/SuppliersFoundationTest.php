<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\SupplierContact;
use App\Modules\BusinessOperations\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuppliersFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_be_created_for_organization(): void
    {
        $organization = Organization::factory()->create();

        $supplier = app(SupplierService::class)->createForOrganization($organization, [
            'name' => 'Acme Supplies',
            'slug' => 'acme-supplies',
            'supplier_code' => 'ACME',
        ]);

        $this->assertNotNull($supplier->ulid);
        $this->assertTrue($supplier->organization->is($organization));
        $this->assertSame('active', $supplier->status);
    }

    public function test_supplier_can_have_primary_contact(): void
    {
        $supplier = app(SupplierService::class)->createForOrganization(Organization::factory()->create(), [
            'name' => 'Acme Supplies',
            'slug' => 'acme-supplies',
        ]);
        $service = app(SupplierService::class);

        $first = $service->addContact($supplier, ['name' => 'First Contact', 'is_primary' => true]);
        $second = $service->addContact($supplier, ['name' => 'Second Contact', 'is_primary' => true]);

        $this->assertFalse($first->refresh()->is_primary);
        $this->assertTrue($second->refresh()->is_primary);
        $this->assertSame(1, SupplierContact::query()->where('supplier_id', $supplier->id)->where('is_primary', true)->count());
    }
}
