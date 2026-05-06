<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\SupplierContact;

class SupplierService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): Supplier
    {
        return Supplier::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addContact(Supplier $supplier, array $attributes): SupplierContact
    {
        $contact = $supplier->contacts()->create([
            ...$attributes,
            'is_primary' => $attributes['is_primary'] ?? false,
        ]);

        if ($contact->is_primary) {
            $this->makePrimaryContact($contact);
        }

        return $contact->refresh();
    }

    public function makePrimaryContact(SupplierContact $contact): SupplierContact
    {
        SupplierContact::query()
            ->where('supplier_id', $contact->supplier_id)
            ->whereKeyNot($contact->id)
            ->update(['is_primary' => false]);

        $contact->forceFill(['is_primary' => true])->save();

        return $contact->refresh();
    }
}
