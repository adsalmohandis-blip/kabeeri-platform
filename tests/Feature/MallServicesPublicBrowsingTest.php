<?php

namespace Tests\Feature;

use App\Models\MallMirrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallServicesPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_page_lists_published_services_only(): void
    {
        MallMirrorService::factory()->create([
            'service_name' => 'Published Service',
            'slug' => 'published-service',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorService::factory()->create([
            'service_name' => 'Draft Service',
            'slug' => 'draft-service',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall/services')
            ->assertOk()
            ->assertSee('Published Service')
            ->assertDontSee('Draft Service');
    }

    public function test_service_detail_renders_published_service(): void
    {
        $service = MallMirrorService::factory()->create([
            'service_name' => 'Public Service',
            'slug' => 'public-service',
            'description' => 'A published service.',
            'service_category' => 'Consulting',
            'hourly_rate' => 150,
            'currency' => 'USD',
            'availability_info' => ['response_time' => '24 hours'],
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/services/'.$service->slug)
            ->assertOk()
            ->assertSee('Public Service')
            ->assertSee('A published service.')
            ->assertSee('Consulting')
            ->assertSee('USD 150.00')
            ->assertSee('24 hours');
    }

    public function test_service_detail_hides_unpublished_service(): void
    {
        $service = MallMirrorService::factory()->create([
            'slug' => 'hidden-service',
            'mirror_status' => 'needs_review',
        ]);

        $this->get('/mall/services/'.$service->slug)
            ->assertNotFound();
    }
}
