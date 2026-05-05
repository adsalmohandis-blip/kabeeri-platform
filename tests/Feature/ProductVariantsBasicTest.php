<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantsBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_have_options_values_and_variants(): void
    {
        $product = Product::factory()->create(['price' => 100, 'sku' => 'BASE']);
        $option = ProductOption::factory()->create([
            'product_id' => $product->id,
            'name' => 'Size',
            'slug' => 'size',
        ]);
        $value = ProductOptionValue::factory()->create([
            'product_option_id' => $option->id,
            'value' => 'Large',
            'slug' => 'large',
        ]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'sku' => 'BASE-L',
            'price' => 120,
            'option_values' => ['size' => 'large'],
        ]);

        $this->assertTrue($product->options()->whereKey($option->id)->exists());
        $this->assertTrue($option->values()->whereKey($value->id)->exists());
        $this->assertTrue($product->variants()->whereKey($variant->id)->exists());
        $this->assertSame('120.00', $variant->price);
        $this->assertSame('BASE-L', $variant->sku);
        $this->assertSame(['size' => 'large'], $variant->option_values);
    }
}
