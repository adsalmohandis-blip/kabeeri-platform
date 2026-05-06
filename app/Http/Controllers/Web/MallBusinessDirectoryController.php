<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorBusiness;
use Illuminate\Contracts\View\View;

class MallBusinessDirectoryController extends Controller
{
    public function index(): View
    {
        $businesses = MallMirrorBusiness::query()
            ->where('mirror_status', 'published')
            ->orderBy('display_name')
            ->paginate(24);

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
