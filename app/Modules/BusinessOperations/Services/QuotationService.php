<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class QuotationService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes = []): Quotation
    {
        $this->assertBelongsToOrganization($organization, Contact::class, 'contact_id', $attributes);
        $this->assertBelongsToOrganization($organization, Lead::class, 'lead_id', $attributes);

        return Quotation::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'quotation_number' => $attributes['quotation_number'] ?? $this->nextQuotationNumber($organization),
            'status' => 'draft',
            'currency_code' => $attributes['currency_code'] ?? 'EGP',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addItem(Quotation $quotation, array $attributes): Quotation
    {
        $quantity = (int) ($attributes['quantity'] ?? 1);
        $unitPrice = (float) ($attributes['unit_price'] ?? 0);
        $discount = (float) ($attributes['discount_total'] ?? 0);
        $tax = (float) ($attributes['tax_total'] ?? 0);

        $quotation->items()->create([
            ...$attributes,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'total' => max(0, ($quantity * $unitPrice) - $discount + $tax),
        ]);

        return $this->recalculateTotals($quotation);
    }

    public function recalculateTotals(Quotation $quotation): Quotation
    {
        $items = $quotation->items()->get();
        $subtotal = $items->sum(fn ($item): float => (float) $item->quantity * (float) $item->unit_price);
        $discount = $items->sum(fn ($item): float => (float) $item->discount_total);
        $tax = $items->sum(fn ($item): float => (float) $item->tax_total);

        $quotation->forceFill([
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'total' => max(0, $subtotal - $discount + $tax),
        ])->save();

        return $quotation->refresh();
    }

    public function issue(Quotation $quotation): Quotation
    {
        if ($quotation->items()->doesntExist()) {
            throw ValidationException::withMessages([
                'items' => 'Quotation must have at least one item before it can be issued.',
            ]);
        }

        $quotation->forceFill([
            'status' => 'issued',
            'issued_at' => now(),
        ])->save();

        return $quotation->refresh();
    }

    public function accept(Quotation $quotation): Quotation
    {
        if ($quotation->status !== 'issued') {
            throw ValidationException::withMessages([
                'status' => 'Only issued quotations can be accepted.',
            ]);
        }

        $quotation->forceFill([
            'status' => 'accepted',
            'accepted_at' => now(),
            'declined_at' => null,
            'decline_reason' => null,
        ])->save();

        return $quotation->refresh();
    }

    public function decline(Quotation $quotation, ?string $reason = null): Quotation
    {
        if ($quotation->status !== 'issued') {
            throw ValidationException::withMessages([
                'status' => 'Only issued quotations can be declined.',
            ]);
        }

        $quotation->forceFill([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $reason,
        ])->save();

        return $quotation->refresh();
    }

    protected function nextQuotationNumber(Organization $organization): string
    {
        $next = Quotation::query()
            ->where('organization_id', $organization->id)
            ->count() + 1;

        return 'QT-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     */
    protected function assertBelongsToOrganization(
        Organization $organization,
        string $modelClass,
        string $key,
        array $attributes
    ): void {
        if (($attributes[$key] ?? null) === null) {
            return;
        }

        $model = $modelClass::query()->findOrFail($attributes[$key]);

        if ((int) $model->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                $key => 'The selected quotation record does not belong to this organization.',
            ]);
        }
    }
}
