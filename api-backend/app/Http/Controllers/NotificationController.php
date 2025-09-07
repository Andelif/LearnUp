<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /** GET /api/notifications */
    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications();

        if (isset($notifications['error'])) {
            return response()->json(['message' => $notifications['error']], $notifications['status']);
        }

        return response()->json($notifications, 200);
    }

    /** PUT /api/notifications/{id}/read */
    public function markAsRead($id)
    {
        $response = $this->notificationService->markNotificationAsRead((int)$id);

        if (isset($response['error'])) {
            return response()->json(['message' => $response['error']], $response['status']);
        }

        return response()->json(['message' => $response['message']], $response['status']);
    }

    /** POST /api/notifications */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // For personal notifications, user_id is required and must exist
            // For broadcast (everyone/all_learner/all_tutor/all_admin), you may omit user_id
            'user_id' => 'nullable|exists:users,id',
            'Message' => 'required|string',
            'Type'    => 'required|in:Tuition Request,Application Update,New Message,Admin Message,General',
            'view'    => 'nullable|string|max:50', // 'everyone'|'all_learner'|'all_tutor'|'all_admin' or null
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = $this->notificationService->createNotification($validator->validated());

        return response()->json(
            ['message' => $response['message'], 'data' => $response['data']],
            $response['status']
        );
    }
}
