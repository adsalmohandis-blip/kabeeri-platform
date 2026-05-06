<?php

namespace Tests\Feature;

use App\Models\TravelTourismMallListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallTravelTourismPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_travel_page_lists_published_listings_only(): void
    {
        TravelTourismMallListing::factory()->create([
            'title' => 'Published Tour',
            'slug' => 'published-tour',
            'listing_status' => 'published',
            'published_at' => now(),
        ]);
        TravelTourismMallListing::factory()->create([
            'title' => 'Draft Tour',
            'slug' => 'draft-tour',
            'listing_status' => 'draft',
        ]);

        $this->get('/mall/travel')
            ->assertOk()
            ->assertSee('Published Tour')
            ->assertDontSee('Draft Tour');
    }

    public function test_travel_detail_renders_published_listing(): void
    {
        $listing = TravelTourismMallListing::factory()->create([
            'title' => 'Public Trip',
            'slug' => 'public-trip',
            'description' => 'A published trip.',
            'destination' => 'Luxor',
            'price_from' => 300,
            'currency' => 'USD',
            'listing_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/travel/'.$listing->slug)
            ->assertOk()
            ->assertSee('Public Trip')
            ->assertSee('A published trip.')
            ->assertSee('Luxor')
            ->assertSee('USD 300.00');
    }

    public function test_travel_detail_hides_unpublished_listing(): void
    {
        $listing = TravelTourismMallListing::factory()->create([
            'slug' => 'hidden-trip',
            'listing_status' => 'needs_review',
        ]);

        $this->get('/mall/travel/'.$listing->slug)
            ->assertNotFound();
    }
}
