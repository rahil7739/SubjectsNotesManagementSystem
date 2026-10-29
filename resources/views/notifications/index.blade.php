<x-layout title="Notifications">
    <section class="panel" aria-labelledby="notifications-heading">
        <div class="section-header">
            <div>
                <p class="eyebrow">Updates</p>
                <h1 id="notifications-heading">Notifications</h1>
                <p class="notification-summary">
                    @if ($unreadCount > 0)
                        You have {{ $unreadCount }} unread notification{{ $unreadCount === 1 ? '' : 's' }}.
                    @else
                        You’re all caught up.
                    @endif
                </p>
            </div>
            @if ($unreadCount > 0)
                <form method="post" action="{{ $isAdminNotificationInbox ? route('admin.notifications.read-all') : route('notifications.read-all') }}">
                    @csrf
                    <button class="button-ghost" type="submit">Mark all as read</button>
                </form>
            @endif
        </div>

        @if (session('status'))
            <p class="status-message" role="status">{{ session('status') }}</p>
        @endif

        @forelse ($notifications as $notification)
            @php($notificationData = $notification->data)
            <article class="notification-card{{ $notification->read_at ? ' is-read' : ' is-unread' }}">
                <span class="notification-status-dot" aria-hidden="true"></span>
                <div class="notification-copy">
                    <h2>{{ $notificationData['title'] ?? 'Notification' }}</h2>
                    <p>{{ $notificationData['message'] ?? 'You have a new update.' }}</p>
                    <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->format('M j, Y \a\t g:i A') }}</time>
                </div>
                <form method="post" action="{{ $isAdminNotificationInbox ? route('admin.notifications.read', $notification->id) : route('notifications.read', $notification->id) }}">
                    @csrf
                    <button class="inline-link notification-open-button" type="submit">{{ $notification->read_at ? 'Open' : 'View' }}</button>
                </form>
            </article>
        @empty
            <div class="empty-state">
                <h2>No notifications yet</h2>
                <p>Updates about subjects and notes assigned to you will appear here.</p>
            </div>
        @endforelse

        @if ($notifications->hasPages())
            <nav class="pagination" aria-label="Notification pages">{{ $notifications->links() }}</nav>
        @endif
    </section>
</x-layout>
