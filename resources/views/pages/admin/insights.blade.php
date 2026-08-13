<x-layouts.app>
    @php
        $typeIcons = [
            'positive' => ['bg-green-100 text-green-700', 'M4.5 12.75l6 6 9-13.5'],
            'warning' => ['bg-amber-100 text-amber-700', 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
            'critical' => ['bg-red-100 text-red-700', 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
            'opportunity' => ['bg-blue-100 text-blue-700', 'M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 015.814-5.518l2.74-1.22m0 0l-5.94-2.281m5.94 2.28l-2.28 5.941'],
        ];
        $priorityBadges = ['high' => 'red', 'medium' => 'amber', 'low' => 'blue'];
    @endphp

    <x-slot:title>Business Insights</x-slot:title>
    <x-slot:subtitle>Automated operational and financial intelligence</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.insights') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.insights') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-card label="Utilization" :value="$kpis['utilization']" suffix="%" icon="percent" accent="brand" />
        <x-stat-card label="Revenue" :value="'IDR ' . number_format($kpis['revenue'], 0)" icon="trending-up" accent="green" />
        <x-stat-card label="Active Rentals" :value="$kpis['active_rentals']" icon="file" accent="blue" />
        <x-stat-card label="Available Fleet" :value="$kpis['available']" icon="layers" accent="navy" />
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <h2 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-navy-900">Insights</h2>
            <div class="space-y-4">
                @forelse ($insights as $insight)
                    @php $icon = $typeIcons[$insight['type']] ?? $typeIcons['warning']; @endphp
                    <div class="card flex gap-4 p-5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $icon[0] }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon[1] }}" /></svg>
                        </span>
                        <div>
                            <h3 class="font-semibold text-navy-900">{{ $insight['title'] }}</h3>
                            <p class="mt-1 text-sm text-charcoal-600">{{ $insight['message'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="card p-6 text-center text-sm text-charcoal-400">No insights generated.</div>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-navy-900">Recommendations</h2>
            <div class="space-y-4">
                @forelse ($recommendations as $rec)
                    <div class="card p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block rounded-full bg-charcoal-100 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-charcoal-500">Priority</span>
                                <x-badge type="{{ $priorityBadges[$rec['priority']] ?? 'gray' }}" class="ml-2">{{ ucfirst($rec['priority']) }}</x-badge>
                            </div>
                        </div>
                        <h3 class="mt-2 font-semibold text-navy-900">{{ $rec['title'] }}</h3>
                        <p class="mt-1 text-sm text-charcoal-600">{{ $rec['action'] }}</p>
                    </div>
                @empty
                    <div class="card p-6 text-center text-sm text-charcoal-400">No recommendations.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>