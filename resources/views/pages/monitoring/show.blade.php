<x-layouts.app>
    @php
        $statusBadges = ['available' => 'green', 'rented' => 'blue', 'maintenance' => 'amber', 'unavailable' => 'gray'];
        $utilTrend = $equipment->utilization()->orderBy('date')->get();
        $labels = $utilTrend->map(fn ($u) => $u->date->format('M'))->unique()->values();
        $hours = $utilTrend->groupBy(fn ($u) => $u->date->format('M'))->map(fn ($g) => round($g->avg('hours_operated'), 1))->values();
    @endphp

    <x-slot:title>Monitoring · {{ $equipment->name }}</x-slot:title>
    <x-slot:subtitle>{{ $equipment->equipment_code }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <x-badge type="{{ $statusBadges[$equipment->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ $equipment->status_label }}</x-badge>
        <a href="{{ route('admin.monitoring.index') }}" class="btn-outline btn-md">Back to Monitoring</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-1">
            @if ($equipment->primaryImage())
                <img src="{{ $equipment->primaryImage()->url }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $equipment->name }}" class="h-52 w-full object-cover">
            @else
                <div class="flex h-52 w-full items-center justify-center bg-charcoal-100 text-4xl font-bold text-charcoal-300">{{ strtoupper(substr($equipment->brand ?? 'E', 0, 1)) }}</div>
            @endif
            <dl class="space-y-3 p-6 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Category</dt><dd class="text-right font-semibold text-navy-900">{{ $equipment->category?->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Brand / Model</dt><dd class="text-right font-semibold text-navy-900">{{ $equipment->brand }} {{ $equipment->model }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Year</dt><dd class="text-right text-navy-900">{{ $equipment->year }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Operating Hours</dt><dd class="text-right font-semibold text-navy-900">{{ number_format($equipment->operating_hours, 0) }} hrs</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Next Service</dt><dd class="text-right text-navy-900">{{ $equipment->next_service_hours ? number_format($equipment->next_service_hours, 0) . ' hrs' : '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Location</dt><dd class="max-w-[55%] text-right text-navy-900">{{ $equipment->current_location ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Region</dt><dd class="text-right text-navy-900">{{ $equipment->region ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Utilization</h3><p class="text-sm text-charcoal-500">{{ $equipment->utilizationRate(now()->startOfYear()) }}% this year</p></div>
                <div class="p-6">
                    <canvas id="utilization-chart" data-labels='{{ $labels->values()->toJson() }}' data-hours='{{ $hours->toJson() }}' class="h-72"></canvas>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="card">
                    <div class="card-header"><h3 class="text-lg font-semibold">Assigned Operators</h3></div>
                    <div class="p-6">
                        @forelse ($equipment->assignedOperators as $op)
                            <a href="{{ route('admin.operators.show', $op->id) }}" class="flex items-center justify-between rounded-lg border border-charcoal-100 px-4 py-3 hover:border-brand-400">
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $op->name }}</p>
                                    <p class="text-xs text-charcoal-500">{{ $op->operator_code }}</p>
                                </div>
                                <x-badge type="{{ $op->availability === 'available' ? 'green' : 'blue' }}">{{ ucfirst($op->availability) }}</x-badge>
                            </a>
                        @empty
                            <p class="text-sm text-charcoal-400">No operators assigned.</p>
                        @endforelse
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3 class="text-lg font-semibold">Recent Maintenance</h3></div>
                    <div class="p-6 space-y-3">
                        @forelse ($equipment->maintenanceRecords->sortByDesc('date')->take(4) as $m)
                            <a href="{{ route('admin.maintenance.show', $m->id) }}" class="flex items-center justify-between rounded-lg border border-charcoal-100 px-4 py-3 hover:border-brand-400">
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $m->title }}</p>
                                    <p class="text-xs text-charcoal-500">{{ $m->date->format('d M Y') }} · {{ ucfirst($m->type) }}</p>
                                </div>
                                <span class="text-xs font-semibold text-brand-600">IDR {{ number_format($m->cost, 0) }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-charcoal-400">No maintenance records.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const utilizationCanvas = document.getElementById('utilization-chart');
        if (utilizationCanvas && window.Chart) {
            new Chart(utilizationCanvas, {
                type: 'line',
                data: {
                    labels: JSON.parse(utilizationCanvas.dataset.labels),
                    datasets: [{
                        label: 'Avg Hours Operated',
                        data: JSON.parse(utilizationCanvas.dataset.hours),
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
                        y: { beginAtZero: true, grid: { color: '#eef0f4' } },
                        x: { grid: { display: false } },
                    }
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>