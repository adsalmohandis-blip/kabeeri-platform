<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorCourse;
use App\Models\MallMirrorProduct;
use App\Models\MallMirrorService;
use App\Models\MallMirrorTalent;
use App\Models\TravelTourismMallListing;
use Illuminate\Contracts\View\View;

class MallHomeController extends Controller
{
    public function __invoke(): View
    {
        return view('mall.index', [
            'pageTitle' => 'KABEERI Mall',
            'sections' => [
                [
                    'label' => 'Business Directory',
                    'route' => route('mall.businesses.index'),
                    'count' => MallMirrorBusiness::query()->where('mirror_status', 'published')->count(),
                ],
                [
                    'label' => 'Products',
                    'route' => route('mall.products.index'),
                    'count' => MallMirrorProduct::query()->where('mirror_status', 'published')->count(),
                ],
                [
                    'label' => 'Services',
                    'route' => route('mall.services.index'),
                    'count' => MallMirrorService::query()->where('mirror_status', 'published')->count(),
                ],
                [
                    'label' => 'Courses',
                    'route' => route('mall.courses.index'),
                    'count' => MallMirrorCourse::query()->where('mirror_status', 'published')->count(),
                ],
                [
                    'label' => 'Talent',
                    'route' => route('mall.talent.index'),
                    'count' => MallMirrorTalent::query()->where('mirror_status', 'published')->count(),
                ],
                [
                    'label' => 'Travel',
                    'route' => route('mall.travel.index'),
                    'count' => TravelTourismMallListing::query()->where('mirror_status', 'published')->count(),
                ],
            ],
        ]);
    }
}
