<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorBusiness;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallBusinessDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $businesses = V13ExternalExperience::applyMallSearch(
            MallMirrorBusiness::query()->where('mirror_status', 'published'),
            'businesses',
            $request->query('q'),
        )
            ->orderBy('display_name')
            ->paginate(24)
            ->withQueryString();

        return view('mall.business-directory.index', [
            'businesses' => $businesses,
            'pageTitle' => 'Business Directory',
        ]);
    }

    public function show(MallMirrorBusiness $business): View
    {
        if ($business->mirror_status !== 'published') {
            abort(404);
        }

        return view('mall.business-directory.show', [
            'business' => $business,
            'pageTitle' => $business->display_name,
        ]);
    }
}
