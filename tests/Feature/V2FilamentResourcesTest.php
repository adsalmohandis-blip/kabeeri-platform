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
use App\Models\OrganizationMembership;
use App\Models\Product;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V2FilamentResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_v2_resources_are_registered_with_expected_pages(): void
    {
        foreach ([
            MenuResource::class,
            RedirectResource::class,
            FormResource::class,
            ImportJobResource::class,
            ProductResource::class,
            OrderResource::class,
            CouponResource::class,
        ] as $resource) {
            $this->assertArrayHasKey('index', $resource::getPages());
            $this->assertArrayHasKey('view', $resource::getPages());
        }

        $this->assertArrayHasKey('create', MenuResource::getPages());
        $this->assertArrayHasKey('edit', ProductResource::getPages());
        $this->assertArrayHasKey('edit', CouponResource::getPages());
        $this->assertArrayNotHasKey('create', OrderResource::getPages());
        $this->assertArrayNotHasKey('create', ImportJobResource::getPages());
    }

    public function test_v2_resources_only_return_accessible_organization_records(): void
    {
        [$user, $organization, $site] = $this->ownedSite();

        $menu = Menu::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $redirect = Redirect::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $form = Form::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $product = Product::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $order = Order::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $coupon = Coupon::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $importJob = ImportJob::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);

        Menu::factory()->create();
        Redirect::factory()->create();
        Form::factory()->create();
        Product::factory()->create();
        Order::factory()->create();
        Coupon::factory()->create();
        ImportJob::factory()->create();

        $this->actingAs($user);

        $this->assertSame([$menu->id], MenuResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$redirect->id], RedirectResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$form->id], FormResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$product->id], ProductResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$order->id], OrderResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$coupon->id], CouponResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$importJob->id], ImportJobResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_v2_resource_index_pages_render_for_owner(): void
    {
        [$user, $organization, $site] = $this->ownedSite();

        Menu::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        Product::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        Order::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        Coupon::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        ImportJob::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);

        $this->actingAs($user);

        foreach ([
            MenuResource::class,
            RedirectResource::class,
            FormResource::class,
            ImportJobResource::class,
            ProductResource::class,
            OrderResource::class,
            CouponResource::class,
        ] as $resource) {
            $this->get($resource::getUrl('index'))->assertOk();
        }
    }

    /**
     * @return array{0: User, 1: Organization, 2: Site}
     */
    private function ownedSite(): array
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $user->id]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'membership_type' => 'owner',
            'status' => 'active',
        ]);

        $site = Site::factory()->create(['organization_id' => $organization->id]);

        return [$user, $organization, $site];
    }
}
