<x-layouts.app>
    @php
        $actionBadges = [
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'login' => 'navy',
            'logout' => 'gray',
            'status' => 'amber',
        ];
    @endphp

    <x-slot:title>Audit Log</x-slot:title>
    <x-slot:subtitle>Complete record of system activity</x-slot:subtitle>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.audit') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-64" placeholder="Search action / entity / description…">
                <select name="user_id" class="input !w-56" onchange="this.form.submit()">
                    <option value="">All Users</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.audit') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Timestamp</th><th>User</th><th>Action</th><th>Entity</th><th>Description</th><th class="hidden lg:table-cell">IP Address</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-xs">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}</td>
                            <td>
                                <span class="font-semibold text-navy-900">{{ $log->user?->name ?? 'System' }}</span>
                                <span class="block text-xs text-charcoal-500">{{ $log->user?->role ?? '—' }}</span>
                            </td>
                            <td><x-badge type="{{ $actionBadges[$log->action] ?? 'gray' }}">{{ ucfirst($log->action) }}</x-badge></td>
                            <td>
                                <span class="font-semibold text-navy-900">{{ ucfirst($log->entity_type) }}</span>
                                <span class="block text-xs text-charcoal-500">#{{ $log->entity_id }}</span>
                            </td>
                            <td class="max-w-md text-sm text-charcoal-600">{{ $log->description }}</td>
                            <td class="hidden text-xs lg:table-cell">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-charcoal-400">No activity found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$logs" /></div>
    </div>
</x-layouts.app>