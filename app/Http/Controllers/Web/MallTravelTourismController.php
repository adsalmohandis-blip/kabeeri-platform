<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TravelTourismMallListing;
use Illuminate\Contracts\View\View;

class MallTravelTourismController extends Controller
{
    public function index(): View
    {
        $listings = TravelTourismMallListing::query()
            ->where('listing_status', 'published')
            ->orderBy('title')
            ->paginate(24);

        return view('mall.travel.index', [
            'listings' => $listings,
            'pageTitle' => 'Travel and Tourism',
        ]);
    }

    public function show(TravelTourismMallListing $listing): View
    {
        if ($listing->listing_status !== 'published') {
            abort(404);
        }

        return view('mall.travel.show', [
            'listing' => $listing,
            'pageTitle' => $listing->title,
        ]);
    }
}
