<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\ImportJob;
use App\Models\RedirectSuggestion;
use App\Modules\CMS\Services\RedirectSuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_suggestion_is_generated_without_creating_redirect(): void
    {
        $job = ImportJob::factory()->create();
        $contentType = ContentType::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'slug' => 'page',
        ]);
        $entry = ContentEntry::factory()->create([
            'organization_id' => $job->organization_id,
            'site_id' => $job->site_id,
            'content_type_id' => $contentType->id,
            'slug' => 'new-about',
            'status' => 'published',
        ]);

        $suggestion = app(RedirectSuggestionService::class)->suggestForContent(
            $job,
            'https://legacy.example.test/about/',
            $entry,
        );

        $this->assertSame('/about/', $suggestion->source_url);
        $this->assertSame('pending', $suggestion->status);
        $this->assertTrue($job->redirectSuggestions()->whereKey($suggestion->id)->exists());
        $this->assertDatabaseCount('redirects', 0);
    }

    public function test_approved_suggestion_creates_actual_redirect(): void
    {
        $suggestion = RedirectSuggestion::factory()->create([
            'source_url' => '/old-page',
            'target_url' => '/app/site/new-page',
        ]);

        $redirect = app(RedirectSuggestionService::class)->approve($suggestion);

        $this->assertSame('/old-page', $redirect->source_path);
        $this->assertSame('/app/site/new-page', $redirect->target_url);
        $this->assertSame('approved', $suggestion->refresh()->status);
    }
}
