<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Redirect;
use App\Models\Site;
use App\Modules\CMS\Services\RedirectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RedirectManagerFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_can_be_created_and_resolved_for_site(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $service = app(RedirectService::class);

        $redirect = $service->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_path' => 'old-page',
            'target_url' => '/new-page',
            'status_code' => 301,
            'metadata' => ['suggested_by' => 'test'],
        ]);

        $resolved = $service->resolve('/old-page', $organization->id, $site->id);

        $this->assertSame($redirect->id, $resolved?->id);
        $this->assertSame('/old-page', $redirect->source_path);
        $this->assertSame('/new-page', $resolved?->target_url);
        $this->assertSame(301, $resolved?->status_code);
        $this->assertSame(['suggested_by' => 'test'], $resolved?->metadata);
        $this->assertTrue($site->redirects()->whereKey($redirect->id)->exists());
    }

    public function test_redirect_hit_count_can_be_incremented_safely(): void
    {
        $redirect = Redirect::factory()->create(['hit_count' => 0]);
        $service = app(RedirectService::class);

        $updated = $service->recordHit($redirect);

        $this->assertSame(1, $updated->hit_count);
        $this->assertNotNull($updated->last_hit_at);
    }

    public function test_source_path_is_unique_per_site_scope(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $otherSite = Site::factory()->create(['organization_id' => $organization->id]);
        $service = app(RedirectService::class);

        $service->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_path' => '/legacy',
            'target_url' => '/current',
        ]);

        $service->create([
            'organization_id' => $organization->id,
            'site_id' => $otherSite->id,
            'source_path' => '/legacy',
            'target_url' => '/other-current',
        ]);

        $this->expectException(ValidationException::class);

        $service->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'source_path' => '/legacy',
            'target_url' => '/duplicate',
        ]);
    }

    public function test_inactive_redirects_do_not_resolve(): void
    {
        $redirect = Redirect::factory()->create(['status' => 'inactive']);
        $service = app(RedirectService::class);

        $this->assertNull(
            $service->resolve($redirect->source_path, $redirect->organization_id, $redirect->site_id),
        );
    }
}
