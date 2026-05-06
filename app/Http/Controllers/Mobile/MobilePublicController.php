<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Modules\Platform\Services\MobileAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePublicController extends Controller
{
    public function config(Request $request, MobileAppService $mobile): JsonResponse
    {
        $config = $mobile->activeConfig($request->query('app_key')) ?? $mobile->createDefaultConfig();

        return response()->json([
            'app_key' => $config->app_key,
            'name' => $config->name,
            'platform' => $config->platform,
            'current_version' => $config->current_version,
            'min_supported_version' => $config->min_supported_version,
            'settings' => $config->settings ?? [],
        ]);
    }

    public function manifest(Request $request, MobileAppService $mobile): JsonResponse
    {
        $config = $mobile->activeConfig($request->query('app_key')) ?? $mobile->createDefaultConfig();
        $manifest = \App\Models\MobileApiManifest::query()
            ->where('mobile_app_config_id', $config->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        return response()->json([
            'version' => $manifest?->version ?? 'v1',
            'endpoints' => $manifest?->endpoints ?? [],
            'capabilities' => $manifest?->capabilities ?? [],
        ]);
    }

    public function theme(Request $request, MobileAppService $mobile): JsonResponse
    {
        $config = $mobile->activeConfig($request->query('app_key')) ?? $mobile->createDefaultConfig();
        $theme = \App\Models\MobileThemeProfile::query()
            ->where('mobile_app_config_id', $config->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        return response()->json([
            'name' => $theme?->name,
            'colors' => $theme?->colors ?? [],
            'typography' => $theme?->typography ?? [],
            'layout' => $theme?->layout ?? [],
        ]);
    }
}
