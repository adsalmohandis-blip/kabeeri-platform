<?php

namespace Tests\Feature;

use App\Models\MallMirrorProduct;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use App\Modules\Mall\Services\MallMirrorProductService;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_mall_mirror_product_can_be_created(): void
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $mirrorProduct = app(MallMirrorProductService::class)->createDraft($product);

        $this->assertSame('Test Product', $mirrorProduct->product_name);
        $this->assertSame('test-product', $mirrorProduct->slug);
        $this->assertSame('99.99', $mirrorProduct->price);
        $this->assertSame('draft', $mirrorProduct->mirror_status);
        $this->assertTrue($product->mallMirrorProducts()->whereKey($mirrorProduct->id)->exists());
    }

    public function test_mirror_product_requires_granted_products_consent_before_publish(): void
    {
        $product = Product::factory()->create();
        $service = app(MallMirrorProductService::class);
        $mirrorProduct = $service->createDraft($product);

        $this->expectValidationFailure(fn () => $service->publish($mirrorProduct));

        $consent = app(MallPublicationConsentService::class)->request(
            $product->organization,
            subject: $product,
            channels: ['products'],
        );
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $mirrorProduct = $service->createDraft($product, $consent);
        $published = $service->publish($mirrorProduct);

        $this->assertSame('published', $published->mirror_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_mirror_product_can_be_unpublished(): void
    {
        $product = Product::factory()->create();
        $consent = app(MallPublicationConsentService::class)->grant(
            app(MallPublicationConsentService::class)->request($product->organization, subject: $product, channels: ['products']),
            User::factory()->create(),
        );
        $service = app(MallMirrorProductService::class);
        $mirrorProduct = $service->publish($service->createDraft($product, $consent));

        $unpublished = $service->unpublish($mirrorProduct);

        $this->assertSame('unpublished', $unpublished->mirror_status);
        $this->assertNull($unpublished->published_at);
    }

    public function test_mirror_product_with_consent(): void
    {
        $organization = Organization::factory()->create();
        $consent = MallPublicationConsent::factory()->create(['organization_id' => $organization->id]);

        $mirrorProduct = MallMirrorProduct::factory()->create([
            'organization_id' => $organization->id,
            'mall_publication_consent_id' => $consent->id,
        ]);

        $this->assertInstanceOf(MallPublicationConsent::class, $mirrorProduct->publicationConsent);
    }

    public function test_mirror_product_rejects_cross_tenant_consent(): void
    {
        $product = Product::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create([
            'organization_id' => Organization::factory()->create()->id,
        ]);

        $this->expectException(ValidationException::class);

        app(MallMirrorProductService::class)->createDraft($product, $foreignConsent);
    }

    public function test_mirror_product_slug_is_unique_per_organization(): void
    {
        $organization = Organization::factory()->create();

        MallMirrorProduct::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-slug',
        ]);

        $this->expectException(QueryException::class);

        MallMirrorProduct::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-slug',
        ]);
    }

    public function test_mirror_product_can_be_soft_deleted(): void
    {
        $mirrorProduct = MallMirrorProduct::factory()->create();

        $this->assertNull($mirrorProduct->deleted_at);

        $mirrorProduct->delete();

        $this->assertNotNull($mirrorProduct->refresh()->deleted_at);
        $this->assertCount(0, MallMirrorProduct::all());
        $this->assertCount(1, MallMirrorProduct::withTrashed()->get());
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
