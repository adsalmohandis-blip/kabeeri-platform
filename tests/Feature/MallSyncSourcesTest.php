<?php

namespace Tests\Feature;

use App\Models\ExternalSource;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Mall\Services\MallSyncSourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallSyncSourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mall_sync_source_can_wrap_external_source_as_preview_first(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $externalSource = ExternalSource::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_type' => 'csv_feed',
            'source_name' => 'Catalog CSV',
        ]);

        $source = app(MallSyncSourceService::class)->create($organization, $site, $externalSource, [
            'sync_scope' => 'products',
            'settings' => ['mode' => 'preview_first'],
        ]);

        $this->assertSame($organization->id, $source->organization_id);
        $this->assertSame($externalSource->id, $source->external_source_id);
        $this->assertSame('products', $source->sync_scope);
        $this->assertSame('mirror_to_mall', $source->sync_direction);
        $this->assertSame('draft', $source->status);
        $this->assertTrue($organization->mallSyncSources()->whereKey($source->id)->exists());
        $this->assertTrue($site->mallSyncSources()->whereKey($source->id)->exists());
        $this->assertTrue($externalSource->mallSyncSources()->whereKey($source->id)->exists());
    }

    public function test_mall_sync_source_requires_preview_before_activation(): void
    {
        $source = app(MallSyncSourceService::class)->create(Organization::factory()->create());
        $service = app(MallSyncSourceService::class);

        $this->expectValidationFailure(fn () => $service->activate($source));

        $previewed = $service->markPreviewed($source);
        $active = $service->activate($previewed);

        $this->assertSame('active', $active->status);
        $this->assertNotNull($active->last_preview_at);
    }

    public function test_mall_sync_source_rejects_cross_tenant_and_plain_secret_settings(): void
    {
        $organization = Organization::factory()->create();
        $foreignSite = Site::factory()->create();
        $foreignExternalSource = ExternalSource::factory()->create();
        $service = app(MallSyncSourceService::class);

        $this->expectValidationFailure(fn () => $service->create($organization, $foreignSite));
        $this->expectValidationFailure(fn () => $service->create($organization, externalSource: $foreignExternalSource));
        $this->expectValidationFailure(fn () => $service->create($organization, attributes: [
            'settings' => ['api_key' => 'plain-text'],
        ]));
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
