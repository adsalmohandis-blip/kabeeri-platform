<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorService;
use Illuminate\Contracts\View\View;

class MallServiceController extends Controller
{
    public function index(): View
    {
        $services = MallMirrorService::query()
            ->where('mirror_status', 'published')
            ->orderBy('service_name')
            ->paginate(24);

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
