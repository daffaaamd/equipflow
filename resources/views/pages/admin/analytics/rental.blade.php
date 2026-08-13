<x-layouts.app>
    <x-slot:title>Rental Analytics</x-slot:title>
    <x-slot:subtitle>Pipeline activity, conversion, and contract trends</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.analytics.rental') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.analytics.rental') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="card mb-6">
        <div class="card-header"><h3 class="text-lg font-semibold">Rental Funnel</h3><p class="text-sm text-charcoal-500">Requests → Quotations → Contracts → Completed</p></div>
        <div class="grid grid-cols-2 gap-6 p-6 sm:grid-cols-4">
            @php
                $funnel = $rentalFunnel;
                $conversion = $funnel['requests'] > 0 ? round(($funnel['contracts'] / $funnel['requests']) * 100, 1) : 0;
                $closeRate = $funnel['quotations'] > 0 ? round(($funnel['contracts'] / $funnel['quotations']) * 100, 1) : 0;
            @endphp
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Requests</p>
                <p class="mt-1 font-display text-4xl font-bold text-navy-900">{{ number_format($funnel['requests']) }}</p>
                <p class="mt-1 text-xs text-charcoal-500">100%</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Quotations</p>
                <p class="mt-1 font-display text-4xl font-bold text-brand-500">{{ number_format($funnel['quotations']) }}</p>
                <p class="mt-1 text-xs text-charcoal-500">{{ $funnel['requests'] > 0 ? round(($funnel['quotations'] / $funnel['requests']) * 100, 1) : 0 }}% of requests</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Contracts</p>
                <p class="mt-1 font-display text-4xl font-bold text-navy-900">{{ number_format($funnel['contracts']) }}</p>
                <p class="mt-1 text-xs text-charcoal-500">{{ $conversion }}% conversion</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Completed</p>
                <p class="mt-1 font-display text-4xl font-bold text-green-600">{{ number_format($funnel['completed']) }}</p>
                <p class="mt-1 text-xs text-charcoal-500">{{ $closeRate }}% close rate</p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Rental Activity</h3><p class="text-sm text-charcoal-500">Requests, quotations, and contracts per month</p></div>
            <div class="p-6">
                <canvas id="activity-chart" data-data='{{ json_encode($rentalActivity) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Contract Duration Distribution</h3><p class="text-sm text-charcoal-500">Days per contract</p></div>
            <div class="p-6">
                <canvas id="duration-chart" data-data='{{ json_encode($duration) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Revenue Trend</h3><p class="text-sm text-charcoal-500">Last 12 months</p></div>
        <div class="p-6">
            <canvas id="revenue-chart" data-labels='{{ json_encode($revenueTrend['labels']) }}' data-values='{{ json_encode($revenueTrend['values']) }}' class="h-72"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        if (el('activity-chart') && window.Chart) {
            const d = JSON.parse(el('activity-chart').dataset.data);
            new Chart(el('activity-chart'), {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [
                        { label: 'Requests', data: d.requests, borderColor: '#0f172a', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 3 },
                        { label: 'Quotations', data: d.quotations, borderColor: '#f97316', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 3 },
                        { label: 'Contracts', data: d.contracts, borderColor: '#22c55e', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 3 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' } }, x: { grid: { display: false } } }
                }
            });
        }
        if (el('duration-chart') && window.Chart) {
            const d = JSON.parse(el('duration-chart').dataset.data);
            new Chart(el('duration-chart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(d),
                    datasets: [{ label: 'Contracts', data: Object.values(d), backgroundColor: 'rgba(15, 23, 42, 0.75)', borderRadius: 6, borderSkipped: false }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' } }, x: { grid: { display: false } } }
                }
            });
        }
        if (el('revenue-chart') && window.Chart) {
            new Chart(el('revenue-chart'), {
                type: 'line',
                data: {
                    labels: JSON.parse(el('revenue-chart').dataset.labels),
                    datasets: [{
                        label: 'Revenue',
                        data: JSON.parse(el('revenue-chart').dataset.values),
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: v => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } }, x: { grid: { display: false } } }
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>