<?php

namespace Tests\Feature;

use App\Models\MallMirrorTalent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallTalentPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_page_lists_published_profiles_only(): void
    {
        MallMirrorTalent::factory()->create([
            'display_name' => 'Published Talent',
            'slug' => 'published-talent',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorTalent::factory()->create([
            'display_name' => 'Draft Talent',
            'slug' => 'draft-talent',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall/talent')
            ->assertOk()
            ->assertSee('Published Talent')
            ->assertDontSee('Draft Talent');
    }

    public function test_talent_detail_renders_published_profile(): void
    {
        $talent = MallMirrorTalent::factory()->create([
            'display_name' => 'Public Talent',
            'slug' => 'public-talent',
            'headline' => 'Product Designer',
            'bio' => 'A published profile.',
            'skills' => ['design', 'research'],
            'location_label' => 'Cairo',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/talent/'.$talent->slug)
            ->assertOk()
            ->assertSee('Public Talent')
            ->assertSee('Product Designer')
            ->assertSee('A published profile.')
            ->assertSee('design, research')
            ->assertSee('Cairo');
    }

    public function test_talent_detail_hides_unpublished_profile(): void
    {
        $talent = MallMirrorTalent::factory()->create([
            'slug' => 'hidden-talent',
            'mirror_status' => 'needs_review',
        ]);

        $this->get('/mall/talent/'.$talent->slug)
            ->assertNotFound();
    }
}
