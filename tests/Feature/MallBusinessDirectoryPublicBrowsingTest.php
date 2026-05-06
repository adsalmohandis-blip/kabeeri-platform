<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallBusinessDirectoryPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_directory_lists_published_businesses_only(): void
    {
        MallMirrorBusiness::factory()->create([
            'display_name' => 'Published Business',
            'slug' => 'published-business',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorBusiness::factory()->create([
            'display_name' => 'Draft Business',
            'slug' => 'draft-business',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall/businesses')
            ->assertOk()
            ->assertSee('Published Business')
            ->assertDontSee('Draft Business');
    }

    public function test_business_directory_detail_renders_published_business(): void
    {
        $business = MallMirrorBusiness::factory()->create([
            'display_name' => 'Public Directory Business',
            'slug' => 'public-directory-business',
            'description' => 'A published public business.',
            'public_contacts' => ['email' => 'hello@example.test'],
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/businesses/'.$business->slug)
            ->assertOk()
            ->assertSee('Public Directory Business')
            ->assertSee('A published public business.')
            ->assertSee('hello@example.test');
    }

    public function test_business_directory_detail_hides_unpublished_business(): void
    {
        $business = MallMirrorBusiness::factory()->create([
            'slug' => 'hidden-business',
            'mirror_status' => 'needs_review',
        ]);

        $this->get('/mall/businesses/'.$business->slug)
            ->assertNotFound();
    }
}
