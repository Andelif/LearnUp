<?php

namespace App\Http\Controllers;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    
    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications();

        if (isset($notifications['error'])) {
            return response()->json(['message' => $notifications['error']], $notifications['status']);
        }

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $response = $this->notificationService->markNotificationAsRead($id);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'Message' => 'required|string',
            'Type' => 'required|in:Tuition Request,Application Update,New Message,Admin Message,General',
            'view' => 'required|string'
        ]);

        $response = $this->notificationService->createNotification($request->all());

        return response()->json(['message' => $response['message']], $response['status']);
    }
}

