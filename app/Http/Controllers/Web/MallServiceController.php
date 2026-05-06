<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorService;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = V13ExternalExperience::applyMallSearch(
            MallMirrorService::query()->where('mirror_status', 'published'),
            'services',
            $request->query('q'),
        )
            ->orderBy('service_name')
            ->paginate(24)
            ->withQueryString();

        return view('mall.services.index', [
            'services' => $services,
            'pageTitle' => 'Services',
        ]);
    }

    public function show(MallMirrorService $service): View
    {
        if ($service->mirror_status !== 'published') {
            abort(404);
        }

        return view('mall.services.show', [
            'service' => $service,
            'pageTitle' => $service->service_name,
        ]);
    }
}
