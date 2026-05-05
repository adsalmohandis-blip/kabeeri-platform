<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['ulid', 'organization_id', 'site_id', 'user_id', 'session_id', 'status', 'currency_code', 'metadata'])]
class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $cart): void {
            if (blank($cart->ulid)) {
                $cart->ulid = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
