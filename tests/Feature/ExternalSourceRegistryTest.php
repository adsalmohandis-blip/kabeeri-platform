<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ExternalSource;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Core\Services\ExternalSourceRegistryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ExternalSourceRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_source_can_be_registered(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $source = app(ExternalSourceRegistryService::class)->register($organization, [
            'source_type' => 'wordpress',
            'source_name' => 'Legacy WordPress',
            'source_url' => 'https://legacy.example.test',
        ], site: $site);

        $this->assertSame('wordpress', $source->source_type);
        $this->assertSame('manual', $source->connection_type);
        $this->assertSame('draft', $source->status);
        $this->assertNull($source->last_sync_at);
    }

    public function test_external_source_registry_rejects_cross_tenant_scope_and_plain_secrets(): void
    {
        $organization = Organization::factory()->create();
        $foreignCompany = Company::factory()->create();
        $foreignSite = Site::factory()->create();
        $service = app(ExternalSourceRegistryService::class);

        $this->expectValidationFailure(fn () => $service->register($organization, [
            'source_name' => 'Foreign Company',
        ], company: $foreignCompany));

        $this->expectValidationFailure(fn () => $service->register($organization, [
            'source_name' => 'Foreign Site',
        ], site: $foreignSite));

        $this->expectValidationFailure(fn () => $service->register($organization, [
            'source_name' => 'Plain Secret',
            'settings' => ['api_key' => 'plain-text'],
        ]));
    }

    public function test_external_source_preview_does_not_mark_live_sync(): void
    {
        $source = ExternalSource::factory()->create(['status' => 'draft', 'last_sync_at' => null]);

        $previewed = app(ExternalSourceRegistryService::class)->markPreviewed($source);

        $this->assertSame('previewed', $previewed->status);
        $this->assertNull($previewed->last_sync_at);
    }

    private function expectValidationFailure(callable $callback): void
    {
        try {
            $callback();
        } catch (ValidationException) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail('Expected validation exception.');
    }
}
