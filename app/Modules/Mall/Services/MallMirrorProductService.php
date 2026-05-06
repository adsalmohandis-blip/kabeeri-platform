<?php

namespace App\Modules\Mall\Services;

use App\Models\MallMirrorProduct;
use App\Models\MallPublicationConsent;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class MallMirrorProductService
{
    public function createDraft(Product $product, ?MallPublicationConsent $consent = null): MallMirrorProduct
    {
        if ($consent !== null && (int) $consent->organization_id !== (int) $product->organization_id) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Publication consent must belong to the product organization.',
            ]);
        }

        return MallMirrorProduct::query()->updateOrCreate(
            [
                'organization_id' => $product->organization_id,
                'slug' => $product->slug,
            ],
            [
                'company_id' => $product->company_id,
                'site_id' => $product->site_id,
                'product_id' => $product->id,
                'mall_publication_consent_id' => $consent?->id,
                'product_name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'currency' => $product->currency_code ?? 'USD',
                'attributes' => [
                    'sku' => $product->sku,
                    'stock_status' => $product->stock_status,
                    'short_description' => $product->short_description,
                ],
                'mirror_status' => 'draft',
                'last_refreshed_at' => now(),
            ],
        );
    }

    public function publish(MallMirrorProduct $product): MallMirrorProduct
    {
        $consent = $product->publicationConsent;

        if ($consent === null || ! app(MallPublicationConsentService::class)->allowsPublication($consent, 'products')) {
            throw ValidationException::withMessages([
                'mall_publication_consent_id' => 'Product publication requires granted Mall products consent.',
            ]);
        }

        $product->forceFill([
            'mirror_status' => 'published',
            'published_at' => now(),
        ])->save();

        return $product->refresh();
    }

    public function unpublish(MallMirrorProduct $product): MallMirrorProduct
    {
        $product->forceFill([
            'mirror_status' => 'unpublished',
            'published_at' => null,
        ])->save();

        return $product->refresh();
    }
}
