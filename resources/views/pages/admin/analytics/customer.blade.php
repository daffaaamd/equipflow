<x-layouts.app>
    <x-slot:title>Customer Analytics</x-slot:title>
    <x-slot:subtitle>Customer segments, growth, and revenue contribution</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.analytics.customer') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.analytics.customer') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Customers" :value="$metrics['total']" icon="users" accent="navy" />
        <x-stat-card label="Active" :value="$metrics['active']" icon="check" accent="green" />
        <x-stat-card label="New (This Year)" :value="$metrics['new']" icon="user-plus" accent="brand" />
        <x-stat-card label="Returning" :value="$metrics['returning']" icon="refresh" accent="blue" />
        <x-stat-card label="Retention Rate" :value="$metrics['retention']" suffix="%" icon="percent" accent="green" />
        <x-stat-card label="Rental Frequency" :value="$metrics['rental_frequency']" icon="activity" accent="amber" />
        <x-stat-card label="Total Rental Value" :value="'IDR ' . number_format($metrics['total_rental_value'], 0)" icon="trending-up" accent="brand" />
        <x-stat-card label="Avg Value / Customer" :value="'IDR ' . number_format($metrics['average_rental_value'], 0)" icon="wallet" accent="navy" />
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Customer Growth</h3><p class="text-sm text-charcoal-500">Cumulative customers per month</p></div>
            <div class="p-6">
                <canvas id="growth-chart" data-labels='{{ json_encode($growth['labels']) }}' data-values='{{ json_encode($growth['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Customer Segments</h3><p class="text-sm text-charcoal-500">Distribution by segment</p></div>
            <div class="p-6">
                <canvas id="segment-chart" data-data='{{ json_encode($segments) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">New vs Returning</h3><p class="text-sm text-charcoal-500">Payers in the selected period</p></div>
            <div class="p-6">
                <canvas id="nvr-chart" data-data='{{ json_encode($newVsReturning) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue by Customer</h3><p class="text-sm text-charcoal-500">Top customers</p></div>
            <div class="p-6">
                <canvas id="customer-chart" data-labels='{{ json_encode($revenueByCustomer['labels']) }}' data-values='{{ json_encode($revenueByCustomer['values']) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const el = (id) => document.getElementById(id);
        if (el('growth-chart') && window.Chart) {
            new Chart(el('growth-chart'), {
                type: 'line',
                data: {
                    labels: JSON.parse(el('growth-chart').dataset.labels),
                    datasets: [{
                        label: 'Customers',
                        data: JSON.parse(el('growth-chart').dataset.values),
                        borderColor: '#0f172a',
                        backgroundColor: 'rgba(15, 23, 42, 0.12)',
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
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' } }, x: { grid: { display: false } } }
                }
            });
        }
        if (el('segment-chart') && window.Chart) {
            const d = JSON.parse(el('segment-chart').dataset.data);
            new Chart(el('segment-chart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(d),
                    datasets: [{ data: Object.values(d), backgroundColor: ['#f97316', '#0f172a', '#3b82f6', '#22c55e', '#f59e0b', '#8b5cf6'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
            });
        }
        if (el('nvr-chart') && window.Chart) {
            const d = JSON.parse(el('nvr-chart').dataset.data);
            new Chart(el('nvr-chart'), {
                type: 'doughnut',
                data: {
                    labels: ['New', 'Returning'],
                    datasets: [{ data: Object.values(d), backgroundColor: ['#f97316', '#0f172a'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
            });
        }
        if (el('customer-chart') && window.Chart) {
            new Chart(el('customer-chart'), {
                type: 'bar',
                data: {
                    labels: JSON.parse(el('customer-chart').dataset.labels),
                    datasets: [{
                        label: 'Revenue',
                        data: JSON.parse(el('customer-chart').dataset.values),
                        backgroundColor: 'rgba(249, 115, 22, 0.75)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: v => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } }, x: { grid: { display: false }, ticks: { maxRotation: 45 } } }
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>