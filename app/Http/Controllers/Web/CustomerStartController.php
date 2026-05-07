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
        $selectedPath = $request->query('path', 'business_owner');
        $paths = config('kabeeri_customer.audience_paths', []);

        if (! is_string($selectedPath) || ! array_key_exists($selectedPath, $paths)) {
            $selectedPath = 'business_owner';
        }

        return view('customer.v16-start', [
            'paths' => $paths,
            'appTypes' => config('kabeeri_customer.app_types', []),
            'themes' => $workspace->starterThemes(is_string($appType) ? $appType : null),
            'selectedAppType' => is_string($appType) ? $appType : null,
            'selectedPath' => $selectedPath,
        ]);
    }
}
