<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MobileDevice;
use App\Modules\Platform\Services\MobileAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDeviceController extends Controller
{
    public function store(Request $request, MobileAppService $mobile): JsonResponse
    {
        $validated = $request->validate([
            'device_uuid' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:40'],
            'app_version' => ['nullable', 'string', 'max:40'],
        ]);

        $device = $mobile->registerDevice(null, $mobile->activeConfig() ?? $mobile->createDefaultConfig(), $validated);

        return response()->json(['device_id' => $device->id, 'status' => $device->status], 201);
    }

    public function pushToken(Request $request, MobileAppService $mobile): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer', 'exists:mobile_devices,id'],
            'provider' => ['nullable', 'string', 'max:40'],
            'token' => ['required', 'string', 'max:4096'],
        ]);

        $record = $mobile->registerPushToken(
            MobileDevice::query()->findOrFail($validated['device_id']),
            $validated['provider'] ?? 'fcm',
            $validated['token'],
        );

        return response()->json(['push_token_id' => $record->id, 'status' => $record->status], 201);
    }
}
