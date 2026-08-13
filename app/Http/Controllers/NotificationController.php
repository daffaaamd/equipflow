<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('pages.notifications.index', compact('notifications'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return $notification->link ? redirect($notification->link) : back();
    }

    public function apiUnreadCount(Request $request)
    {
        return response()->json(['count' => NotificationService::unreadCount($request->user()->id)]);
    }
}