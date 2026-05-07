<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\CustomerWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerStartController extends Controller
{
    public function __invoke(Request $request, CustomerWorkspaceService $workspace): View
    {
        $appType = $request->query('app_type');

        return view('customer.v16-start', [
            'paths' => config('kabeeri_customer.audience_paths', []),
            'appTypes' => config('kabeeri_customer.app_types', []),
            'themes' => $workspace->starterThemes(is_string($appType) ? $appType : null),
            'selectedAppType' => is_string($appType) ? $appType : null,
        ]);
    }
}
