<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentRevision;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsCoreFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_page_and_post_content_types(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $pageType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $postType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Post',
            'slug' => 'post',
        ]);

        $this->assertDatabaseHas('content_types', [
            'id' => $pageType->id,
            'name' => 'Page',
            'slug' => 'page',
            'organization_id' => $organization->id,
        ]);

        $this->assertDatabaseHas('content_types', [
            'id' => $postType->id,
            'name' => 'Post',
            'slug' => 'post',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_can_create_content_entry_and_revision(): void
    {
        $author = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $author->id]);
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $author->id,
        ]);

        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Post',
            'slug' => 'post',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'author_user_id' => $author->id,
            'title' => 'My First Post',
            'slug' => 'my-first-post',
            'status' => 'draft',
        ]);

        $revision = ContentRevision::factory()->create([
            'content_entry_id' => $entry->id,
            'created_by' => $author->id,
            'title' => 'My First Post (Draft)',
        ]);

        $this->assertDatabaseHas('content_entries', [
            'id' => $entry->id,
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'my-first-post',
        ]);

        $this->assertDatabaseHas('content_revisions', [
            'id' => $revision->id,
            'content_entry_id' => $entry->id,
            'created_by' => $author->id,
        ]);
    }

    public function test_can_assign_taxonomy_term_to_content_entry(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Post',
            'slug' => 'post',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'slug' => 'taxonomy-test-post',
        ]);

        $taxonomy = Taxonomy::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Categories',
            'slug' => 'categories',
            'type' => 'category',
        ]);

        $term = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        $entry->taxonomyTerms()->attach($term->id);

        $this->assertDatabaseHas('content_taxonomy_term', [
            'content_entry_id' => $entry->id,
            'taxonomy_term_id' => $term->id,
        ]);

        $this->assertTrue($entry->fresh()->taxonomyTerms->contains('id', $term->id));
    }
}
