<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['content_entry_id', 'taxonomy_term_id'])]
class ContentTaxonomyTerm extends Model
{
    use HasFactory;

    protected $table = 'content_taxonomy_term';

    public $incrementing = false;

    protected $primaryKey = null;

    public function contentEntry(): BelongsTo
    {
        return $this->belongsTo(ContentEntry::class);
    }

    public function taxonomyTerm(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTerm::class);
    }
}
