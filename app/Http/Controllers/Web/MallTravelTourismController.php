<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TravelTourismMallListing;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallTravelTourismController extends Controller
{
    public function index(Request $request): View
    {
        $listings = V13ExternalExperience::applyMallSearch(
            TravelTourismMallListing::query()->where('listing_status', 'published'),
            'travel',
            $request->query('q'),
        )
            ->orderBy('title')
            ->paginate(24)
            ->withQueryString();

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
