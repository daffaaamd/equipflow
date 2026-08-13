<x-layouts.app>
    @php
        $statusBadges = ['available' => 'green', 'rented' => 'blue', 'maintenance' => 'amber', 'unavailable' => 'gray'];
    @endphp

    <x-slot:title>Fleet Monitoring</x-slot:title>
    <x-slot:subtitle>Live status of the equipment fleet</x-slot:subtitle>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Available</p>
            <p class="mt-1 font-display text-3xl font-bold text-green-600">{{ $available }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Rented</p>
            <p class="mt-1 font-display text-3xl font-bold text-blue-600">{{ $rented }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">In Maintenance</p>
            <p class="mt-1 font-display text-3xl font-bold text-amber-600">{{ $inMaintenance }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Service Due Soon</p>
            <p class="mt-1 font-display text-3xl font-bold {{ $dueService > 0 ? 'text-red-600' : 'text-navy-900' }}">{{ $dueService }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.monitoring.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search equipment…">
                <select name="category" class="input !w-44" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($filters['category'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['available', 'rented', 'maintenance', 'unavailable'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <select name="region" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Regions</option>
                    @foreach (['Jabodetabek', 'Java', 'Sumatra', 'Kalimantan', 'Sulawesi', 'Bali & Nusa Tenggara', 'Papua'] as $r)
                        <option value="{{ $r }}" @selected(($filters['region'] ?? '') === $r)>{{ $r }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.monitoring.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Equipment</th><th>Category</th><th>Region</th><th>Location</th><th>Hours</th><th>Service Due</th><th>Utilization</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($equipment as $eq)
                        @php $due = $eq->next_service_hours ? $eq->next_service_hours - $eq->operating_hours : null; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.monitoring.show', $eq->id) }}" class="font-semibold text-navy-900 hover:text-brand-500">{{ $eq->name }}</a>
                                <span class="block text-xs text-charcoal-500">{{ $eq->equipment_code }}</span>
                            </td>
                            <td>{{ $eq->category?->name }}</td>
                            <td>{{ $eq->region ?? '—' }}</td>
                            <td class="max-w-[12rem] truncate text-xs">{{ $eq->current_location ?? '—' }}</td>
                            <td>{{ number_format($eq->operating_hours, 0) }}</td>
                            <td>
                                @if ($due !== null)
                                    @if ($due < 0)
                                        <span class="font-semibold text-red-600">Overdue {{ number_format(abs($due), 0) }}h</span>
                                    @elseif ($due < 300)
                                        <span class="font-semibold text-amber-600">{{ number_format($due, 0) }}h left</span>
                                    @else
                                        <span class="text-charcoal-500">{{ number_format($due, 0) }}h left</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-charcoal-100">
                                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $eq->utilizationRate(now()->startOfYear(), now()->endOfYear()) }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ $eq->utilizationRate(now()->startOfYear(), now()->endOfYear()) }}%</span>
                                </div>
                            </td>
                            <td><x-badge type="{{ $statusBadges[$eq->status] ?? 'gray' }}">{{ $eq->status_label }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.monitoring.show', $eq->id) }}" class="btn-outline btn-sm">Monitor</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-10 text-center text-charcoal-400">No equipment found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$equipment" /></div>
    </div>
</x-layouts.app>