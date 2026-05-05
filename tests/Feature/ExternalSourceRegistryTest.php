<?php

namespace Tests\Feature;

use App\Models\ExternalSource;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalSourceRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_source_can_be_registered(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $source = ExternalSource::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_type' => 'wordpress',
            'source_name' => 'Legacy WordPress',
            'source_url' => 'https://legacy.example.test',
            'connection_type' => 'manual',
            'status' => 'draft',
        ]);

        $this->assertSame('wordpress', $source->source_type);
        $this->assertSame('manual', $source->connection_type);
        $this->assertSame('draft', $source->status);
        $this->assertNull($source->last_sync_at);
    }
}
