<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Models\MigrationMapping;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Modules\CMS\Services\WordPressTaxonomyMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressTaxonomyMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_and_tags_import_as_taxonomy_terms_with_mappings(): void
    {
        $job = ImportJob::factory()->create();

        $result = app(WordPressTaxonomyMapper::class)->mapTerms($job, [
            ['id' => '10', 'domain' => 'category', 'slug' => 'news', 'name' => 'News'],
            ['id' => '20', 'domain' => 'post_tag', 'slug' => 'launch', 'name' => 'Launch'],
            ['id' => '21', 'domain' => 'post_tag', 'slug' => 'launch', 'name' => 'Launch'],
        ]);

        $this->assertCount(3, $result['terms']);
        $this->assertSame([], $result['warnings']);
        $this->assertSame(2, Taxonomy::query()->count());
        $this->assertSame(2, TaxonomyTerm::query()->count());
        $this->assertDatabaseHas('taxonomy_terms', ['slug' => 'news', 'name' => 'News']);
        $this->assertDatabaseHas('taxonomy_terms', ['slug' => 'launch', 'name' => 'Launch']);
        $this->assertSame(3, MigrationMapping::query()->count());
    }

    public function test_unsupported_taxonomies_return_warnings(): void
    {
        $job = ImportJob::factory()->create();

        $result = app(WordPressTaxonomyMapper::class)->mapTerms($job, [
            ['id' => '30', 'domain' => 'product_cat', 'slug' => 'shirts', 'name' => 'Shirts'],
        ]);

        $this->assertSame([], $result['terms']);
        $this->assertSame('product_cat', $result['warnings'][0]['domain']);
        $this->assertSame(0, TaxonomyTerm::query()->count());
    }
}
