<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    /**
     * Get list of notifications for authenticated user
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'data' => $notifications,
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();
        $notification = Notification::where('user_id', $userId)->where('id', $id)->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $userId = Auth::id();
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Update FCM Token for authenticated user
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user) {
            \Illuminate\Support\Facades\Log::info("FCM Token Received for User {$user->id} ({$user->name}): " . $request->fcm_token);
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token updated successfully',
        ]);
    }
}
