<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Platform\Services\MobileAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function register(Request $request, MobileAppService $mobile): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'device_uuid' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:40'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $device = null;
        if (! empty($validated['device_uuid'])) {
            $device = $mobile->registerDevice($user, $mobile->activeConfig() ?? $mobile->createDefaultConfig(), $validated);
        }

        $token = $mobile->issueToken($user, $device);

        return response()->json([
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'token' => $token['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request, MobileAppService $mobile): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_uuid' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:40'],
        ]);

        $device = null;
        if (! empty($validated['device_uuid'])) {
            $device = $mobile->registerDevice(null, $mobile->activeConfig() ?? $mobile->createDefaultConfig(), $validated);
        }

        $result = $mobile->login($validated['email'], $validated['password'], $device);

        return response()->json([
            'user' => ['id' => $result['user']->id, 'name' => $result['user']->name, 'email' => $result['user']->email],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }
}
