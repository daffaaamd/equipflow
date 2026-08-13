<x-layouts.app>
    @php
        $k = $kpis;
    @endphp

    <x-slot:title>Fleet Analytics</x-slot:title>
    <x-slot:subtitle>Equipment performance, utilization, and profitability</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.analytics.fleet') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.analytics.fleet') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Fleet" :value="$k['total_fleet']" icon="layers" accent="navy" />
        <x-stat-card label="Available" :value="$k['available']" icon="check" accent="green" />
        <x-stat-card label="Rented" :value="$k['rented']" icon="activity" accent="brand" />
        <x-stat-card label="In Maintenance" :value="$k['maintenance']" icon="tool" accent="amber" />
        <x-stat-card label="Utilization" :value="$k['utilization']" suffix="%" icon="percent" accent="brand" />
        <x-stat-card label="Active Rentals" :value="$k['active_rentals']" icon="file" accent="blue" />
        <x-stat-card label="Revenue" :value="'IDR ' . number_format($k['revenue'], 0)" :trend="$k['revenue_trend']" icon="trending-up" accent="green" />
        <x-stat-card label="Maintenance Cost" :value="'IDR ' . number_format($k['maintenance_cost'], 0)" :trend="$k['maintenance_trend']" :trend-good="false" icon="settings" accent="red" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header"><h3 class="text-lg font-semibold">Utilization Trend</h3><p class="text-sm text-charcoal-500">Percentage of rented days over the last 12 months</p></div>
            <div class="p-6">
                <canvas id="util-chart" data-labels='{{ json_encode($utilizationTrend['labels']) }}' data-values='{{ json_encode($utilizationTrend['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Fleet Status</h3><p class="text-sm text-charcoal-500">Current distribution</p></div>
            <div class="p-6">
                <canvas id="status-chart" data-data='{{ json_encode($equipmentStatus) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue by Equipment Type</h3><p class="text-sm text-charcoal-500">Top categories</p></div>
            <div class="p-6">
                <canvas id="type-chart" data-labels='{{ json_encode($revenueByType['labels']) }}' data-values='{{ json_encode($revenueByType['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Top Performing Equipment</h3><p class="text-sm text-charcoal-500">By revenue</p></div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Equipment</th><th>Category</th><th>Hours</th><th class="text-right">Revenue</th><th>Utilization</th></tr></thead>
                    <tbody>
                        @forelse ($topEquipment as $eq)
                            <tr>
                                <td class="font-semibold text-navy-900">{{ $eq['code'] }}</td>
                                <td class="text-xs">{{ $eq['category'] }}</td>
                                <td>{{ number_format($eq['hours'], 0) }}</td>
                                <td class="text-right font-semibold">IDR {{ number_format($eq['revenue'], 0) }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-14 overflow-hidden rounded-full bg-charcoal-100">
                                            <div class="h-full rounded-full bg-brand-500" style="width: {{ $eq['utilization'] }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $eq['utilization'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Profitability Matrix</h3><p class="text-sm text-charcoal-500">Revenue vs maintenance cost by unit</p></div>
        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Equipment</th><th>Utilization</th><th class="text-right">Revenue</th><th class="text-right">Maint. Cost</th><th class="text-right">Profit</th><th class="text-right">Margin</th></tr></thead>
                <tbody>
                    @forelse ($profitability as $p)
                        <tr>
                            <td>
                                <span class="font-semibold text-navy-900">{{ $p['name'] }}</span>
                                <span class="block text-xs text-charcoal-500">{{ $p['code'] }}</span>
                            </td>
                            <td>{{ $p['utilization'] }}%</td>
                            <td class="text-right">IDR {{ number_format($p['revenue'], 0) }}</td>
                            <td class="text-right text-red-600">IDR {{ number_format($p['maintenance_cost'], 0) }}</td>
                            <td class="text-right font-semibold text-green-600">IDR {{ number_format($p['profit'], 0) }}</td>
                            <td class="text-right">
                                <x-badge type="{{ $p['margin'] >= 60 ? 'green' : ($p['margin'] >= 30 ? 'amber' : 'red') }}">{{ $p['margin'] }}%</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-charcoal-400">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        if (el('util-chart') && window.Chart) {
            new Chart(el('util-chart'), {
                type: 'line',
                data: {
                    labels: JSON.parse(el('util-chart').dataset.labels),
                    datasets: [{
                        label: 'Utilization %',
                        data: JSON.parse(el('util-chart').dataset.values),
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
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: '#eef0f4' }, ticks: { callback: v => v + '%' } },
                        x: { grid: { display: false } },
                    }
                }
            });
        }
        if (el('status-chart') && window.Chart) {
            const data = JSON.parse(el('status-chart').dataset.data);
            new Chart(el('status-chart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
                    datasets: [{ data: Object.values(data), backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#9ca3af'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
            });
        }
        if (el('type-chart') && window.Chart) {
            new Chart(el('type-chart'), {
                type: 'bar',
                data: {
                    labels: JSON.parse(el('type-chart').dataset.labels),
                    datasets: [{
                        label: 'Revenue',
                        data: JSON.parse(el('type-chart').dataset.values),
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
    </script>
    @endpush
</x-layouts.app>