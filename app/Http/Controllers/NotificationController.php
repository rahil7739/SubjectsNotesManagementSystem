<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $recipient = $this->recipient($request);

        return view('notifications.index', [
            'notifications' => $recipient->notifications()->latest()->paginate(20),
            'unreadCount' => $recipient->unreadNotifications()->count(),
            'isAdminNotificationInbox' => $request->routeIs('admin.notifications.*'),
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $record = $this->recipient($request)->notifications()->findOrFail($notification);
        $record->markAsRead();

        return redirect()->to($this->notificationUrl($request, $record));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->recipient($request)->unreadNotifications()->update(['read_at' => now()]);

        return to_route($this->routeName($request))->with('status', 'All notifications marked as read.');
    }

    private function recipient(Request $request): User
    {
        $recipient = $request->routeIs('admin.notifications.*')
            ? $request->user('admin')
            : $request->user('web');

        abort_unless($recipient instanceof User, 401);

        return $recipient;
    }

    private function routeName(Request $request): string
    {
        return $request->routeIs('admin.notifications.*')
            ? 'admin.notifications.index'
            : 'notifications.index';
    }

    private function notificationUrl(Request $request, DatabaseNotification $notification): string
    {
        $fallback = route($this->routeName($request));
        $url = $notification->data['url'] ?? $fallback;
        $baseUrl = rtrim(url('/'), '/');

        return is_string($url) && ($url === $baseUrl || str_starts_with($url, $baseUrl.'/'))
            ? $url
            : $fallback;
    }
}
