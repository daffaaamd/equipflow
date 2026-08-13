<x-layouts.app>
    @php
        $k = $kpis;
        $statusLabels = ['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance', 'unavailable' => 'Unavailable'];
        $statusColors = ['available' => '#16a34a', 'rented' => '#f59e0b', 'maintenance' => '#ef4444', 'unavailable' => '#9ca3af'];
        $reqBadges = ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'];
        $conBadges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
    @endphp

    <x-slot:title>Dashboard</x-slot:title>

    {{-- KPI Row --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="Total Fleet" :value="number_format($k['total_fleet'])" accent="navy" icon="fleet" />
        <x-stat-card label="Active Rentals" :value="number_format($k['active_rentals'])" accent="brand" icon="activity" />
        <x-stat-card label="Fleet Utilization" :value="$k['utilization']" suffix="%" accent="green" icon="percent" />
        <x-stat-card label="Revenue (Period)" :value="'IDR ' . number_format($k['revenue'], 0)" :trend="$k['revenue_trend']" accent="blue" icon="trending-up" />
        <x-stat-card label="Maintenance Cost" :value="'IDR ' . number_format($k['maintenance_cost'], 0)" :trend="$k['maintenance_trend']" :trend-good="false" accent="red" icon="tool" />
    </div>

    {{-- Charts row 1 --}}
    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Revenue Trend</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Monthly revenue — last 12 months</p>
            </div>
            <div class="p-6">
                <canvas id="chart-revenue" class="h-72 w-full"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Fleet Utilization</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Rented days as a share of available days</p>
            </div>
            <div class="p-6">
                <canvas id="chart-utilization" class="h-72 w-full"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="mt-5 grid gap-5 md:grid-cols-3">
        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Equipment Status</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Current fleet distribution</p>
            </div>
            <div class="p-6">
                <canvas id="chart-status" class="h-72 w-full"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Rental Funnel</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Requests → Quotations → Contracts</p>
            </div>
            <div class="p-6">
                <canvas id="chart-funnel" class="h-72 w-full"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Revenue by Equipment Type</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Share by category</p>
            </div>
            <div class="p-6">
                <canvas id="chart-revenue-type" class="h-72 w-full"></canvas>
            </div>
        </div>
    </div>

    {{-- Revenue vs target + Top equipment --}}
    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <h3 class="text-base font-semibold">Revenue vs Target</h3>
                <p class="mt-0.5 text-sm text-charcoal-500">Actual vs planned monthly revenue</p>
            </div>
            <div class="p-6">
                <canvas id="chart-target" class="h-72 w-full"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Top Performing Equipment</h3>
                    <p class="text-sm text-charcoal-500">By revenue during the period</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Unit</th><th>Category</th><th>Revenue</th><th>Util.</th></tr></thead>
                    <tbody>
                        @forelse ($topEquipment as $t)
                            <tr>
                                <td>
                                    <p class="font-semibold text-navy-900">{{ $t['code'] }}</p>
                                    <p class="text-xs text-charcoal-500">{{ $t['name'] }}</p>
                                </td>
                                <td>{{ $t['category'] }}</td>
                                <td class="font-semibold">IDR {{ number_format($t['revenue'], 0) }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-16 overflow-hidden rounded-full bg-charcoal-200">
                                            <div class="h-full bg-brand-500" style="width: {{ min(100, $t['utilization']) }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $t['utilization'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Recent Rental Requests</h3>
                    <p class="text-sm text-charcoal-500">Latest submissions</p>
                </div>
                <a href="{{ route('admin.rental-requests.index') }}" class="btn-outline btn-sm">View all</a>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Request</th><th>Customer</th><th>Project</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentRequests as $r)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('admin.rental-requests.show', $r->id) }}" class="hover:text-brand-500">{{ $r->request_number }}</a></td>
                                <td>{{ $r->customer?->company_name }}</td>
                                <td class="max-w-[12rem] truncate">{{ $r->project_name }}</td>
                                <td><x-badge type="{{ $reqBadges[$r->status] ?? 'gray' }}">{{ ucfirst($r->status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No requests</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Recent Contracts</h3>
                    <p class="text-sm text-charcoal-500">Latest agreements</p>
                </div>
                <a href="{{ route('admin.contracts.index') }}" class="btn-outline btn-sm">View all</a>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Contract</th><th>Customer</th><th>Value</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentContracts as $c)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('admin.contracts.show', $c->id) }}" class="hover:text-brand-500">{{ $c->contract_number }}</a></td>
                                <td>{{ $c->customer?->company_name }}</td>
                                <td class="font-semibold">IDR {{ number_format($c->contract_value, 0) }}</td>
                                <td><x-badge type="{{ $conBadges[$c->status] ?? 'gray' }}">{{ ucfirst($c->status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No contracts</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        const fmt = (v) => 'IDR ' + Number(v).toLocaleString('en-US', { maximumFractionDigits: 0 });

        if (window.Chart) {
            if (el('chart-revenue')) {
                new Chart(el('chart-revenue'), {
                    type: 'bar',
                    data: {
                        labels: @json($revenueTrend['labels']),
                        datasets: [{
                            label: 'Revenue',
                            data: @json($revenueTrend['values']),
                            backgroundColor: '#f95f14',
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ' ' + fmt(c.parsed.y) } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: (v) => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            if (el('chart-utilization')) {
                new Chart(el('chart-utilization'), {
                    type: 'line',
                    data: {
                        labels: @json($utilizationTrend['labels']),
                        datasets: [{
                            label: 'Utilization %',
                            data: @json($utilizationTrend['values']),
                            borderColor: '#2a4a6f',
                            backgroundColor: 'rgba(42,74,111,.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100, grid: { color: '#eef0f4' }, ticks: { callback: (v) => v + '%' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            if (el('chart-status')) {
                new Chart(el('chart-status'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Available', 'Rented', 'Maintenance', 'Unavailable'],
                        datasets: [{
                            data: [{{ $equipmentStatus['available'] ?? 0 }}, {{ $equipmentStatus['rented'] ?? 0 }}, {{ $equipmentStatus['maintenance'] ?? 0 }}, {{ $equipmentStatus['unavailable'] ?? 0 }}],
                            backgroundColor: ['#16a34a', '#f59e0b', '#ef4444', '#9ca3af'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }

            if (el('chart-funnel')) {
                new Chart(el('chart-funnel'), {
                    type: 'bar',
                    data: {
                        labels: ['Requests', 'Quotations', 'Contracts', 'Completed'],
                        datasets: [{
                            label: 'Count',
                            data: [{{ $rentalFunnel['requests'] ?? 0 }}, {{ $rentalFunnel['quotations'] ?? 0 }}, {{ $rentalFunnel['contracts'] ?? 0 }}, {{ $rentalFunnel['completed'] ?? 0 }}],
                            backgroundColor: ['#1f3a5a', '#2a4a6f', '#f95f14', '#16a34a'],
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#eef0f4' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            if (el('chart-revenue-type')) {
                new Chart(el('chart-revenue-type'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($revenueByType['labels']),
                        datasets: [{
                            data: @json($revenueByType['values']),
                            backgroundColor: ['#f95f14', '#2a4a6f', '#1f3a5a', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6', '#0ea5e9'],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }

            if (el('chart-target')) {
                new Chart(el('chart-target'), {
                    type: 'line',
                    data: {
                        labels: @json($revenueVsTarget['labels']),
                        datasets: [
                            { label: 'Actual', data: @json($revenueVsTarget['actual']), borderColor: '#f95f14', backgroundColor: 'rgba(249,95,20,.1)', fill: true, tension: 0.3 },
                            { label: 'Target', data: @json($revenueVsTarget['target']), borderColor: '#2a4a6f', borderDash: [6, 4], tension: 0.3 },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { tooltip: { callbacks: { label: (c) => ' ' + c.dataset.label + ': ' + fmt(c.parsed.y) } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: (v) => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }
        }
    </script>
    @endpush
</x-layouts.app>