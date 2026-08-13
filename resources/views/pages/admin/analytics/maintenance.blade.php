<x-layouts.app>
    <x-slot:title>Maintenance Analytics</x-slot:title>
    <x-slot:subtitle>Maintenance costs, downtime, and equipment reliability</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.analytics.maintenance') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.analytics.maintenance') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Service Due Soon" :value="$dueCount" icon="alert" accent="red" />
        <x-stat-card label="Active / Scheduled Jobs" :value="$activeMaintenance" icon="clock" accent="amber" />
        <x-stat-card label="Total Cost (All time)" :value="'IDR ' . number_format($totalCost, 0)" icon="settings" accent="brand" />
        <x-stat-card label="Total Downtime" :value="number_format($totalDowntime, 1)" suffix=" hrs" icon="tool" accent="navy" />
        <x-stat-card label="Total Records" :value="$frequency" icon="layers" accent="blue" />
        <x-stat-card label="Avg Cost / Record" :value="'IDR ' . number_format($frequency > 0 ? $totalCost / $frequency : 0, 0)" icon="percent" accent="green" />
        <x-stat-card label="Avg Downtime / Record" :value="$frequency > 0 ? number_format($totalDowntime / $frequency, 1) : '0.0'" suffix=" hrs" icon="clock" accent="amber" />
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Maintenance Cost Trend</h3><p class="text-sm text-charcoal-500">Last 12 months</p></div>
            <div class="p-6">
                <canvas id="cost-chart" data-labels='{{ json_encode($costTrend['labels']) }}' data-values='{{ json_encode($costTrend['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Maintenance by Equipment</h3><p class="text-sm text-charcoal-500">Frequency and cost per unit</p></div>
            <div class="p-6">
                <canvas id="equip-chart" data-data='{{ json_encode($maintenanceByEquipment) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Maintenance by Equipment — Detail</h3><p class="text-sm text-charcoal-500">Top 10 units</p></div>
        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Equipment</th><th class="text-right">Jobs</th><th class="text-right">Cost</th></tr></thead>
                <tbody>
                    @foreach ($maintenanceByEquipment['labels'] ?? [] as $i => $label)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $label }}</td>
                            <td class="text-right">{{ number_format($maintenanceByEquipment['counts'][$i]) }}</td>
                            <td class="text-right font-semibold">IDR {{ number_format($maintenanceByEquipment['costs'][$i], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        if (el('cost-chart') && window.Chart) {
            new Chart(el('cost-chart'), {
                type: 'line',
                data: {
                    labels: JSON.parse(el('cost-chart').dataset.labels),
                    datasets: [{
                        label: 'Cost',
                        data: JSON.parse(el('cost-chart').dataset.values),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.15)',
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
        if (el('equip-chart') && window.Chart) {
            const d = JSON.parse(el('equip-chart').dataset.data);
            new Chart(el('equip-chart'), {
                type: 'bar',
                data: {
                    labels: d.labels,
                    datasets: [
                        { label: 'Jobs', data: d.counts, backgroundColor: 'rgba(15, 23, 42, 0.75)', borderRadius: 6, borderSkipped: false },
                        { label: 'Cost (IDR)', data: d.costs, backgroundColor: 'rgba(249, 115, 22, 0.75)', borderRadius: 6, borderSkipped: false },
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
    </script>
    @endpush
</x-layouts.app>