<x-layouts.app>
    @php
        $statusBadges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
        $methodBadges = ['bank_transfer' => 'blue', 'cash' => 'green', 'cheque' => 'amber', 'giro' => 'navy'];
    @endphp

    <x-slot:title>Finance Dashboard</x-slot:title>
    <x-slot:subtitle>Revenue, receivables, and profitability overview</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input !w-40">
            <span class="text-sm text-charcoal-400">to</span>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.finance.dashboard') }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Revenue" value="IDR {{ number_format($summary['revenue'], 0) }}" icon="trending-up" accent="brand" />
        <x-stat-card label="Outstanding" value="IDR {{ number_format($summary['outstanding'], 0) }}" icon="clock" accent="amber" />
        <x-stat-card label="Collected" value="IDR {{ number_format($summary['paid'], 0) }}" icon="check" accent="green" />
        <x-stat-card label="Overdue" value="IDR {{ number_format($summary['overdue'], 0) }}" icon="alert" accent="red" />
        <x-stat-card label="Profit" value="IDR {{ number_format($summary['profit'], 0) }}" icon="activity" accent="brand" />
        <x-stat-card label="Profit Margin" value="{{ $summary['margin'] }}%" icon="percent" accent="green" />
        <x-stat-card label="Maintenance Cost" value="IDR {{ number_format($summary['maint_cost'], 0) }}" icon="tool" accent="amber" />
        <x-stat-card label="Operational Cost" value="IDR {{ number_format($summary['operational_cost'], 0) }}" icon="settings" accent="navy" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header"><h3 class="text-lg font-semibold">Revenue Trend</h3><p class="text-sm text-charcoal-500">Last 12 months</p></div>
            <div class="p-6">
                <canvas id="revenue-chart" data-labels='{{ json_encode($revenueTrend['labels']) }}' data-values='{{ json_encode($revenueTrend['values']) }}' class="h-72"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Payment Status</h3><p class="text-sm text-charcoal-500">Invoices by status</p></div>
            <div class="p-6">
                <canvas id="status-chart" data-data='{{ json_encode($paymentStatus) }}' class="h-72"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Recent Payments</h3><a href="{{ route('admin.payments.index') }}" class="text-sm text-brand-600 hover:underline">View all</a></div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Payment</th><th>Invoice</th><th>Method</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($recentPayments as $p)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('admin.payments.show', $p->id) }}" class="hover:text-brand-500">{{ $p->payment_number }}</a></td>
                                <td class="text-xs">{{ $p->invoice?->invoice_number ?? '—' }}</td>
                                <td><x-badge type="{{ $methodBadges[$p->method] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $p->method)) }}</x-badge></td>
                                <td class="text-right font-semibold text-green-600">IDR {{ number_format($p->amount, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No recent payments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Overdue Invoices</h3><a href="{{ route('admin.invoices.index') }}" class="text-sm text-brand-600 hover:underline">View all</a></div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Due</th><th class="text-right">Balance</th></tr></thead>
                    <tbody>
                        @forelse ($overdueInvoices as $inv)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('admin.invoices.show', $inv->id) }}" class="hover:text-brand-500">{{ $inv->invoice_number }}</a></td>
                                <td>{{ $inv->customer?->company_name }}</td>
                                <td class="text-xs">{{ $inv->due_date->format('d M Y') }}</td>
                                <td class="text-right font-semibold text-red-600">IDR {{ number_format($inv->balance, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No overdue invoices. Nice!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const revenueCanvas = document.getElementById('revenue-chart');
        if (revenueCanvas && window.Chart) {
            new Chart(revenueCanvas, {
                type: 'bar',
                data: {
                    labels: JSON.parse(revenueCanvas.dataset.labels),
                    datasets: [{
                        label: 'Revenue (IDR)',
                        data: JSON.parse(revenueCanvas.dataset.values),
                        backgroundColor: 'rgba(249, 115, 22, 0.75)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#eef0f4' }, ticks: { callback: v => 'IDR ' + (v / 1e6).toFixed(0) + 'M' } },
                        x: { grid: { display: false } },
                    }
                }
            });
        }
        const statusCanvas = document.getElementById('status-chart');
        if (statusCanvas && window.Chart) {
            const data = JSON.parse(statusCanvas.dataset.data);
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: { legend: { position: 'bottom' } },
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>