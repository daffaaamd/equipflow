<x-layouts.app>
    <x-slot:title>Finance Analytics</x-slot:title>
    <x-slot:subtitle>Revenue performance, targets, and receivables</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.analytics.finance') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.analytics.finance') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Revenue" :value="'IDR ' . number_format($summary['revenue'], 0)" icon="trending-up" accent="brand" />
        <x-stat-card label="Outstanding" :value="'IDR ' . number_format($summary['outstanding'], 0)" icon="clock" accent="amber" />
        <x-stat-card label="Profit" :value="'IDR ' . number_format($summary['profit'], 0)" icon="activity" accent="green" />
        <x-stat-card label="Profit Margin" :value="$summary['margin']" suffix="%" icon="percent" accent="navy" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue vs Target</h3><p class="text-sm text-charcoal-500">Actual collections vs target</p></div>
            <div class="p-6">
                <canvas id="target-chart" data-labels='{{ json_encode($revenueVsTarget['labels']) }}' data-actual='{{ json_encode($revenueVsTarget['actual']) }}' data-target='{{ json_encode($revenueVsTarget['target']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Payment Status</h3><p class="text-sm text-charcoal-500">Invoice distribution</p></div>
            <div class="p-6">
                <canvas id="status-chart" data-data='{{ json_encode($paymentStatus) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue by Region</h3><p class="text-sm text-charcoal-500">Geographic distribution</p></div>
            <div class="p-6">
                <canvas id="region-chart" data-labels='{{ json_encode($revenueByRegion['labels']) }}' data-values='{{ json_encode($revenueByRegion['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue by Equipment Type</h3><p class="text-sm text-charcoal-500">Top categories</p></div>
            <div class="p-6">
                <canvas id="type-chart" data-labels='{{ json_encode($revenueByType['labels']) }}' data-values='{{ json_encode($revenueByType['values']) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        if (el('target-chart') && window.Chart) {
            new Chart(el('target-chart'), {
                type: 'line',
                data: {
                    labels: JSON.parse(el('target-chart').dataset.labels),
                    datasets: [
                        { label: 'Actual', data: JSON.parse(el('target-chart').dataset.actual), borderColor: '#f97316', backgroundColor: 'rgba(249, 115, 22, 0.15)', fill: true, tension: 0.35, borderWidth: 2, pointRadius: 3 },
                        { label: 'Target', data: JSON.parse(el('target-chart').dataset.target), borderColor: '#0f172a', borderDash: [6, 4], backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 0 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: v => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } }, x: { grid: { display: false } } }
                }
            });
        }
        if (el('status-chart') && window.Chart) {
            const d = JSON.parse(el('status-chart').dataset.data);
            new Chart(el('status-chart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(d).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
                    datasets: [{ data: Object.values(d), backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e', '#ef4444'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
            });
        }
        if (el('region-chart') && window.Chart) {
            new Chart(el('region-chart'), {
                type: 'bar',
                data: {
                    labels: JSON.parse(el('region-chart').dataset.labels),
                    datasets: [{
                        label: 'Revenue',
                        data: JSON.parse(el('region-chart').dataset.values),
                        backgroundColor: 'rgba(15, 23, 42, 0.75)',
                        borderRadius: 6,
                        borderSkipped: false,
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
        if (el('type-chart') && window.Chart) {
            new Chart(el('type-chart'), {
                type: 'doughnut',
                data: {
                    labels: JSON.parse(el('type-chart').dataset.labels),
                    datasets: [{ data: JSON.parse(el('type-chart').dataset.values), backgroundColor: ['#f97316', '#0f172a', '#3b82f6', '#22c55e', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
            });
        }
    </script>
    @endpush
</x-layouts.app>