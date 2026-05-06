<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\Ui\V15PublicRuntime;
use Illuminate\Http\JsonResponse;

class PublicWebRuntimeController extends Controller
{
    public function manifest(): JsonResponse
    {
        return response()->json(V15PublicRuntime::manifest());
    }
}
