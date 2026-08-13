<x-layouts.app>
    @php
        $typeBadges = ['info' => 'blue', 'success' => 'green', 'warning' => 'amber', 'error' => 'red'];
        $typeIcons = [
            'info' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
            'success' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
            'error' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ];
        $typeColors = ['info' => 'bg-blue-100 text-blue-700', 'success' => 'bg-green-100 text-green-700', 'warning' => 'bg-amber-100 text-amber-700', 'error' => 'bg-red-100 text-red-700'];
    @endphp

    <x-slot:title>Notifications</x-slot:title>
    <x-slot:subtitle>All your notifications and alerts</x-slot:subtitle>

    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-charcoal-500">{{ $notifications->total() }} total notification{{ $notifications->total() !== 1 ? 's' : '' }}</p>
        @if ($notifications->whereNull('read_at')->count())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn-outline btn-md">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="divide-y divide-charcoal-100">
            @forelse ($notifications as $notification)
                <div class="flex items-start gap-4 p-5 {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $typeColors[$notification->type] ?? $typeColors['info'] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcons[$notification->type] ?? $typeIcons['info'] }}" /></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-navy-900">{{ $notification->title }}</h3>
                            @unless ($notification->read_at)
                                <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                            @endunless
                            <x-badge type="{{ $typeBadges[$notification->type] ?? 'gray' }}">{{ ucfirst($notification->type) }}</x-badge>
                        </div>
                        <p class="mt-1 text-sm text-charcoal-600">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-charcoal-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if ($notification->link || ! $notification->read_at)
                        <div class="flex shrink-0 flex-col items-end gap-2">
                            @if ($notification->link)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="btn-outline btn-sm">Open</a>
                            @elseif (!$notification->read_at)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="btn-outline btn-sm">Mark read</a>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <p class="text-sm text-charcoal-400">No notifications yet.</p>
                </div>
            @endforelse
        </div>
        <div class="border-t border-charcoal-100 px-5 py-4"><x-pagination :links="$notifications" /></div>
    </div>
</x-layouts.app>