<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use App\Models\MigrationMapping;
use App\Models\User;
use App\Modules\CMS\Services\WordPressAuthorMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressAuthorMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_mapping_can_store_unknown_author_as_metadata(): void
    {
        $job = ImportJob::factory()->create();

        $mapping = app(WordPressAuthorMapper::class)->map($job, [
            'id' => '7',
            'login' => 'legacy-author',
            'email' => 'legacy@example.test',
            'display_name' => 'Legacy Author',
        ]);

        $this->assertSame('wordpress_author', $mapping->source_type);
        $this->assertSame('7', $mapping->source_id);
        $this->assertSame('legacy-author', $mapping->source_key);
        $this->assertSame('metadata_only', $mapping->mapping_status);
        $this->assertSame('store_author_metadata', $mapping->mapping_strategy);
        $this->assertNull($mapping->target_type);
        $this->assertFalse($mapping->metadata['auto_created_user']);
        $this->assertTrue($job->mappings()->whereKey($mapping->id)->exists());
    }

    public function test_unknown_author_can_be_mapped_to_importing_user(): void
    {
        $job = ImportJob::factory()->create();
        $user = User::factory()->create();

        $mapping = app(WordPressAuthorMapper::class)->map($job, [
            'id' => '8',
            'login' => 'unknown-author',
        ], $user);

        $this->assertSame('mapped', $mapping->mapping_status);
        $this->assertSame('fallback_user', $mapping->mapping_strategy);
        $this->assertSame($user->getMorphClass(), $mapping->target_type);
        $this->assertSame($user->id, $mapping->target_id);
        $this->assertSame(1, MigrationMapping::query()->count());
    }
}
