<?php

namespace Database\Factories;

use App\Models\ContentEntry;
use App\Models\ContentTaxonomyTerm;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentTaxonomyTerm>
 */
class ContentTaxonomyTermFactory extends Factory
{
    protected $model = ContentTaxonomyTerm::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_entry_id' => ContentEntry::factory(),
            'taxonomy_term_id' => TaxonomyTerm::factory(),
        ];
    }
}
