<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ImportJob;
use App\Models\ImportRecord;
use App\Models\User;
use App\Modules\CMS\Services\WordPressAuthorMapper;
use App\Modules\CMS\Services\WordPressContentImporter;
use App\Modules\CMS\Services\WordPressTaxonomyMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressContentImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_and_posts_import_as_cms_entries_without_auto_publish_by_default(): void
    {
        $job = ImportJob::factory()->create([
            'settings' => ['allow_publish' => false],
        ]);
        $user = User::factory()->create();

        app(WordPressAuthorMapper::class)->map($job, ['id' => '1', 'login' => 'admin'], $user);
        app(WordPressTaxonomyMapper::class)->mapTerms($job, [
            ['id' => '10', 'domain' => 'category', 'slug' => 'news', 'name' => 'News'],
        ]);

        $entries = app(WordPressContentImporter::class)->import($job, [
            [
                'id' => '100',
                'type' => 'post',
                'title' => 'Hello World',
                'slug' => 'hello-world',
                'status' => 'publish',
                'date' => '2024-01-01 10:00:00',
                'excerpt' => 'Hello excerpt',
                'content_html' => '<p>Hello content</p>',
                'author' => 'admin',
                'terms' => [['domain' => 'category', 'slug' => 'news', 'name' => 'News']],
            ],
            [
                'id' => '101',
                'type' => 'page',
                'title' => 'About',
                'slug' => 'about',
                'status' => 'draft',
                'content_html' => '<p>About content</p>',
                'author' => 'admin',
                'terms' => [],
            ],
        ]);

        $this->assertCount(2, $entries);
        $this->assertSame('draft', $entries[0]->status);
        $this->assertNull($entries[0]->published_at);
        $this->assertSame($user->id, $entries[0]->author_user_id);
        $this->assertSame('<p>Hello content</p>', $entries[0]->body);
        $this->assertTrue($entries[0]->taxonomyTerms()->where('slug', 'news')->exists());
        $this->assertSame('page', $entries[1]->contentType->slug);
        $this->assertSame(2, ImportRecord::query()->where('status', 'imported')->count());
        $this->assertSame(2, ContentEntry::query()->count());
    }

    public function test_publish_status_is_preserved_only_when_import_settings_allow_it(): void
    {
        $job = ImportJob::factory()->create([
            'settings' => ['allow_publish' => true],
        ]);

        $entry = app(WordPressContentImporter::class)->import($job, [[
            'id' => '200',
            'type' => 'page',
            'title' => 'Published Page',
            'slug' => 'published-page',
            'status' => 'publish',
            'date' => '2024-02-01 10:00:00',
        ]])[0];

        $this->assertSame('published', $entry->status);
        $this->assertNotNull($entry->published_at);
    }
}
