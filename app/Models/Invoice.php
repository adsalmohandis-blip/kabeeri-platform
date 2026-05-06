<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'ulid',
    'organization_id',
    'company_id',
    'site_id',
    'contact_id',
    'lead_id',
    'quotation_id',
    'invoice_number',
    'status',
    'payment_status',
    'currency_code',
    'subtotal',
    'discount_total',
    'tax_total',
    'paid_total',
    'total',
    'issued_at',
    'due_at',
    'notes',
    'metadata',
])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $invoice): void {
            if (blank($invoice->ulid)) {
                $invoice->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'paid_total' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'metadata' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
