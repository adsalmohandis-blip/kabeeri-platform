<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\DesktopClient;
use App\Models\DesktopSyncSession;
use App\Models\User;
use App\Modules\Platform\Services\DesktopSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesktopSyncController extends Controller
{
    public function register(Request $request, DesktopSyncService $service): JsonResponse
    {
        $data = $request->validate([
            'client_uuid' => ['required', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['nullable', 'string', 'max:120'],
            'platform' => ['nullable', 'string', 'max:40'],
            'app_version' => ['nullable', 'string', 'max:40'],
            'capabilities' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ]);

        $client = $service->registerClient(
            isset($data['user_id']) ? User::query()->find($data['user_id']) : null,
            $data,
        );

        $session = $service->startSession($client);

        return response()->json([
            'client_id' => $client->id,
            'client_uuid' => $client->client_uuid,
            'session_id' => $session->id,
            'session_uuid' => $session->session_uuid,
            'status' => $client->status,
        ], 201);
    }

    public function pull(Request $request, DesktopSyncService $service): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer', 'exists:desktop_sync_sessions,id'],
            'collections' => ['nullable', 'array'],
            'collections.*' => ['string', 'max:80'],
            'since' => ['nullable', 'string', 'max:120'],
        ]);

        $session = DesktopSyncSession::query()->findOrFail($data['session_id']);

        return response()->json($service->pull(
            $session,
            $data['collections'] ?? ['modules'],
            $data['since'] ?? null,
        ));
    }

    public function pushDryRun(Request $request, DesktopSyncService $service): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer', 'exists:desktop_sync_sessions,id'],
            'operations' => ['required', 'array'],
            'operations.*.client_operation_id' => ['nullable', 'string', 'max:120'],
            'operations.*.operation_type' => ['nullable', 'string', 'max:40'],
            'operations.*.entity_type' => ['required', 'string', 'max:120'],
            'operations.*.entity_id' => ['nullable', 'string', 'max:120'],
            'operations.*.base_version' => ['nullable', 'integer', 'min:0'],
            'operations.*.server_version' => ['nullable', 'integer', 'min:0'],
            'operations.*.payload' => ['nullable', 'array'],
        ]);

        $session = DesktopSyncSession::query()->findOrFail($data['session_id']);

        return response()->json($service->dryRunPush($session, $data['operations']));
    }

    public function queueFile(Request $request, DesktopSyncService $service): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:desktop_clients,id'],
            'session_id' => ['nullable', 'integer', 'exists:desktop_sync_sessions,id'],
            'direction' => ['nullable', 'string', 'max:40'],
            'filename' => ['required', 'string', 'max:255'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'size_bytes' => ['nullable', 'integer', 'min:0'],
            'sha256' => ['nullable', 'string', 'size:64'],
            'metadata' => ['nullable', 'array'],
        ]);

        $client = DesktopClient::query()->findOrFail($data['client_id']);
        $session = isset($data['session_id']) ? DesktopSyncSession::query()->find($data['session_id']) : null;
        $item = $service->queueFile($client, $data, $session);

        return response()->json([
            'queue_id' => $item->id,
            'queue_uuid' => $item->queue_uuid,
            'status' => $item->status,
            'storage_reference' => $item->storage_reference,
        ], 201);
    }
}
