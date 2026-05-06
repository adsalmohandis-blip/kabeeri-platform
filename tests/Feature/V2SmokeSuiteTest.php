<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CsvImport;
use App\Models\ExternalSource;
use App\Models\Form;
use App\Models\ImportJob;
use App\Models\MallSyncSource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Product;
use App\Models\Redirect;
use App\Modules\Mall\Services\WordPressWooCommerceListingSyncPreview;
use Database\Seeders\PaymentMethodsSeeder;
use Database\Seeders\ThemesSeeder;
use Database\Seeders\V1DemoSeeder;
use Database\Seeders\V2DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V2SmokeSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_v2_demo_seed_creates_cms_migration_commerce_and_source_records(): void
    {
        $this->seed([ThemesSeeder::class, PaymentMethodsSeeder::class, V1DemoSeeder::class, V2DemoSeeder::class]);

        $this->assertDatabaseHas('menus', ['slug' => 'v2-demo-main-menu', 'status' => 'active']);
        $this->assertDatabaseHas('menu_items', ['label' => 'Products', 'status' => 'active']);
        $this->assertDatabaseHas('redirects', ['source_path' => '/old-contact', 'status_code' => 301]);
        $this->assertDatabaseHas('forms', ['slug' => 'v2-demo-contact-form', 'status' => 'active']);
        $this->assertDatabaseHas('form_fields', ['name' => 'email', 'field_type' => 'email']);
        $this->assertDatabaseHas('products', ['slug' => 'v2-demo-starter-package', 'status' => 'published']);
        $this->assertDatabaseHas('coupons', ['code' => 'V2DEMO', 'status' => 'active']);
        $this->assertDatabaseHas('orders', ['customer_email' => 'v2-demo-customer@example.com', 'total' => 89.10]);
        $this->assertDatabaseHas('external_sources', ['source_type' => 'woocommerce', 'source_name' => 'V2 Demo WooCommerce']);
        $this->assertDatabaseHas('csv_imports', ['file_path' => 'demo/imports/v2-products.csv', 'status' => 'completed']);
        $this->assertDatabaseHas('import_jobs', ['source_type' => 'wordpress', 'source_name' => 'V2 Demo WordPress']);

        $this->assertSame(1, Menu::query()->where('slug', 'v2-demo-main-menu')->count());
        $this->assertSame(1, Form::query()->where('slug', 'v2-demo-contact-form')->count());
        $this->assertSame(1, Product::query()->where('slug', 'v2-demo-starter-package')->count());
        $this->assertSame(1, Coupon::query()->where('code', 'V2DEMO')->count());
        $this->assertSame(1, Order::query()->where('customer_email', 'v2-demo-customer@example.com')->count());
        $this->assertSame(1, Redirect::query()->where('source_path', '/old-contact')->count());
        $this->assertSame(1, ExternalSource::query()->where('source_name', 'V2 Demo WooCommerce')->count());
        $this->assertSame(1, CsvImport::query()->where('file_path', 'demo/imports/v2-products.csv')->count());
        $this->assertSame(1, ImportJob::query()->where('source_name', 'V2 Demo WordPress')->count());
    }

    public function test_wordpress_woocommerce_preview_remains_preview_only(): void
    {
        $source = MallSyncSource::factory()->create([
            'source_type' => 'woocommerce',
            'sync_scope' => 'products',
            'last_synced_at' => null,
        ]);

        $event = app(WordPressWooCommerceListingSyncPreview::class)->preview($source, [
            'items' => [
                ['type' => 'product', 'name' => 'Preview Product', 'slug' => 'preview-product'],
                ['type' => 'unsupported', 'name' => 'Needs Review', 'slug' => 'needs-review'],
            ],
        ]);

        $this->assertSame('preview', $event->event_type);
        $this->assertSame('completed', $event->status);
        $this->assertSame(1, $event->processed_records);
        $this->assertSame(1, $event->failed_records);
        $this->assertNotNull($source->refresh()->last_preview_at);
        $this->assertNull($source->last_synced_at);
    }
}
