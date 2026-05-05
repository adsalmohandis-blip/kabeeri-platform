<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'method_type', 'status', 'settings'])]
class PaymentMethod extends Model
{
    protected function casts(): array
    {
        return ['settings' => 'array'];
    }
}
