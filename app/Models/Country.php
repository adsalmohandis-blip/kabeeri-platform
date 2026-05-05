<?php

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'iso2', 'iso3', 'phone_code', 'status'])]
class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;
}
