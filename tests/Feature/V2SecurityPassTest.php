<?php

namespace Tests\Feature;

use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Resources\Forms\FormResource;
use App\Filament\Resources\ImportJobs\ImportJobResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Redirects\RedirectResource;
use App\Models\Coupon;
use App\Models\Form;
use App\Models\ImportJob;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V2SecurityPassTest extends TestCase
{
    use RefreshDatabase;

    public function test_v2_resources_return_no_records_when_unauthenticated(): void
    {
        Menu::factory()->create();
        Redirect::factory()->create();
        Form::factory()->create();
        Product::factory()->create();
        Order::factory()->create();
        Coupon::factory()->create();
        ImportJob::factory()->create();

        $this->assertSame(0, MenuResource::getEloquentQuery()->count());
        $this->assertSame(0, RedirectResource::getEloquentQuery()->count());
        $this->assertSame(0, FormResource::getEloquentQuery()->count());
        $this->assertSame(0, ProductResource::getEloquentQuery()->count());
        $this->assertSame(0, OrderResource::getEloquentQuery()->count());
        $this->assertSame(0, CouponResource::getEloquentQuery()->count());
        $this->assertSame(0, ImportJobResource::getEloquentQuery()->count());
    }

    public function test_outsider_cannot_access_foreign_v2_resource_pages(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $menu = Menu::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $product = Product::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $order = Order::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $coupon = Coupon::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $importJob = ImportJob::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);

        $this->actingAs($outsider);

        foreach ([
            MenuResource::getUrl('view', ['record' => $menu]),
            ProductResource::getUrl('view', ['record' => $product]),
            OrderResource::getUrl('view', ['record' => $order]),
            CouponResource::getUrl('view', ['record' => $coupon]),
            ImportJobResource::getUrl('view', ['record' => $importJob]),
        ] as $url) {
            $response = $this->get($url);
            $this->assertContains($response->getStatusCode(), [403, 404]);
        }
    }
}
