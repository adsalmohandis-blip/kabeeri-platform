<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorProduct;
use App\Models\TravelTourismMallListing;
use App\Modules\Mall\Services\MallMirrorStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorStatusesTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_service_can_mark_mirror_models_for_review(): void
    {
        $business = MallMirrorBusiness::factory()->create(['mirror_status' => 'draft']);
        $product = MallMirrorProduct::factory()->create(['mirror_status' => 'draft']);
        $service = app(MallMirrorStatusService::class);

        $reviewBusiness = $service->markNeedsReview($business, 'Missing logo');
        $reviewProduct = $service->markNeedsReview($product, 'Needs price check');

        $this->assertSame('needs_review', $service->status($reviewBusiness));
        $this->assertSame('Missing logo', $reviewBusiness->metadata['status_reason']);
        $this->assertSame('needs_review', $service->status($reviewProduct));
        $this->assertSame('Needs price check', $reviewProduct->metadata['status_reason']);
    }

    public function test_status_service_handles_listing_status_field_for_travel(): void
    {
        $listing = TravelTourismMallListing::factory()->create(['listing_status' => 'draft']);

        $archived = app(MallMirrorStatusService::class)->archive($listing, 'Expired package');

        $this->assertSame('archived', $archived->listing_status);
        $this->assertSame('Expired package', $archived->metadata['status_reason']);
    }

    public function test_status_service_rejects_unsupported_models(): void
    {
        $this->expectException(ValidationException::class);

        app(MallMirrorStatusService::class)->status(new class extends Model {});
    }
}
