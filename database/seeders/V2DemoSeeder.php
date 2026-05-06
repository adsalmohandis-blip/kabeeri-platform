<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\CsvImport;
use App\Models\ExternalSource;
use App\Models\Form;
use App\Models\FormField;
use App\Models\ImportJob;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Redirect;
use App\Models\Site;
use Illuminate\Database\Seeder;

class V2DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->first()
            ?? Organization::query()->first();

        if (! $organization) {
            return;
        }

        $site = Site::query()
            ->where('organization_id', $organization->id)
            ->where('slug', 'kabeeri-demo-app')
            ->first()
            ?? Site::query()->where('organization_id', $organization->id)->first();

        if (! $site) {
            return;
        }

        $menu = Menu::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'v2-demo-main-menu',
            ],
            [
                'name' => 'V2 Demo Main Menu',
                'location' => 'header',
                'status' => 'active',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        foreach ([
            ['label' => 'Home', 'url' => '/app/'.$site->slug.'/home', 'sort_order' => 1],
            ['label' => 'Products', 'url' => '/mall/products', 'sort_order' => 2],
            ['label' => 'Contact', 'url' => '/app/'.$site->slug.'/contact', 'sort_order' => 3],
        ] as $item) {
            MenuItem::query()->updateOrCreate(
                ['menu_id' => $menu->id, 'label' => $item['label']],
                [
                    'url' => $item['url'],
                    'link_type' => 'custom',
                    'target' => 'self',
                    'sort_order' => $item['sort_order'],
                    'status' => 'active',
                    'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
                ],
            );
        }

        Menu::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'v2-demo-footer-menu',
            ],
            [
                'name' => 'V2 Demo Footer Menu',
                'location' => 'footer',
                'status' => 'active',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        Redirect::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'source_path' => '/old-contact',
            ],
            [
                'target_url' => '/app/'.$site->slug.'/contact',
                'status_code' => 301,
                'status' => 'active',
                'hit_count' => 0,
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        $form = Form::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'v2-demo-contact-form',
            ],
            [
                'name' => 'V2 Demo Contact Form',
                'description' => 'Demo lead capture form for V2 CMS and CRM flow.',
                'status' => 'active',
                'submit_button_label' => 'Send Request',
                'success_message' => 'Thanks. The demo team will contact you soon.',
                'settings' => ['lead_capture' => true],
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        foreach ([
            ['label' => 'Name', 'name' => 'name', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['label' => 'Email', 'name' => 'email', 'field_type' => 'email', 'is_required' => true, 'sort_order' => 2],
            ['label' => 'Message', 'name' => 'message', 'field_type' => 'textarea', 'is_required' => false, 'sort_order' => 3],
        ] as $field) {
            FormField::query()->updateOrCreate(
                ['form_id' => $form->id, 'name' => $field['name']],
                $field + ['settings' => ['seeded' => true]],
            );
        }

        $category = ProductCategory::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'v2-demo-services',
            ],
            [
                'name' => 'V2 Demo Services',
                'status' => 'active',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        $product = Product::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'v2-demo-starter-package',
            ],
            [
                'category_id' => $category->id,
                'name' => 'V2 Demo Starter Package',
                'description' => 'A demo Commerce Lite product for validating product, cart, order, and mall-preview flows.',
                'short_description' => 'Starter implementation package.',
                'status' => 'published',
                'visibility' => 'public',
                'price' => 99.00,
                'currency_code' => 'USD',
                'sku' => 'V2-DEMO-STARTER',
                'stock_status' => 'in_stock',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        Coupon::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'code' => 'V2DEMO',
            ],
            [
                'description' => 'Demo coupon for V2 Commerce Lite.',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonth(),
                'usage_limit' => 100,
                'used_count' => 0,
                'status' => 'active',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        $order = Order::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'customer_email' => 'v2-demo-customer@example.com',
            ],
            [
                'customer_name' => 'V2 Demo Customer',
                'customer_phone' => '+20-100-000-0001',
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'currency_code' => 'USD',
                'subtotal' => 99.00,
                'discount_total' => 9.90,
                'total' => 89.10,
                'notes' => 'Seeded V2 demo order.',
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        OrderItem::query()->updateOrCreate(
            ['order_id' => $order->id, 'product_id' => $product->id],
            [
                'name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 1,
                'unit_price' => 99.00,
                'total' => 99.00,
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        $externalSource = ExternalSource::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'source_type' => 'woocommerce',
                'source_name' => 'V2 Demo WooCommerce',
            ],
            [
                'source_url' => 'https://demo-store.example.test',
                'connection_type' => 'manual',
                'status' => 'active',
                'settings' => ['preview_only' => true],
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );

        CsvImport::query()->updateOrCreate(
            ['external_source_id' => $externalSource->id, 'file_path' => 'demo/imports/v2-products.csv'],
            [
                'status' => 'completed',
                'total_rows' => 3,
                'imported_count' => 2,
                'skipped_count' => 1,
                'error_count' => 0,
                'errors' => [],
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
                'started_at' => now()->subMinutes(10),
                'completed_at' => now()->subMinutes(9),
            ],
        );

        ImportJob::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'source_type' => 'wordpress',
                'source_name' => 'V2 Demo WordPress',
            ],
            [
                'status' => 'previewed',
                'summary' => [
                    'posts' => 2,
                    'pages' => 4,
                    'warnings' => ['Shortcode review required for legacy builder blocks.'],
                ],
                'settings' => ['allow_publish' => false],
                'metadata' => ['seeded' => true, 'source' => 'v2_demo'],
            ],
        );
    }
}
