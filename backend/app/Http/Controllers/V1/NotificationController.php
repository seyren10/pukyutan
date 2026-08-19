<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List the authenticated user's notifications, most recent first.
     */
    public function index(Request $request)
    {
        $perPage = min($request->query("per_page", 15), 50);

        return $request->user()
            ->notifications()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return response()->noContent();
    }

    /**
     * Mark every unread notification as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->noContent();
    }
}