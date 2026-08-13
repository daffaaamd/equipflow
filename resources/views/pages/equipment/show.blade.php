<x-layouts.app>
    <x-slot:title>{{ $equipment->equipment_code }}</x-slot:title>
    <x-slot:subtitle>{{ $equipment->name }}</x-slot:subtitle>

    @php
        $badges = ['available' => 'green', 'rented' => 'amber', 'maintenance' => 'red', 'unavailable' => 'gray'];
        $images = $equipment->images->isNotEmpty() ? $equipment->images->map(fn ($i) => $i->url)->all() : ['/img/placeholder.svg'];
        $maintBadges = ['scheduled' => 'amber', 'in_progress' => 'blue', 'completed' => 'green', 'cancelled' => 'gray'];
        $stats = [
            ['Operating Hours', number_format($equipment->operating_hours, 0), 'hours'],
            ['Operating Weight', $equipment->operating_weight ? number_format($equipment->operating_weight, 0) . ' kg' : '—'],
            ['Engine Power', $equipment->engine_power ? number_format($equipment->engine_power, 0) . ' hp' : '—'],
            ['Hours to Service', $equipment->next_service_hours ? number_format(max(0, $equipment->next_service_hours - $equipment->operating_hours), 0) . ' hrs' : '—'],
        ];
    @endphp

    {{-- Top bar --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $badges[$equipment->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ $equipment->status_label }}</x-badge>
            <x-badge type="navy" class="!text-sm !px-3 !py-1">{{ $equipment->category?->name }}</x-badge>
        </div>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.equipment.status', $equipment->id) }}" class="flex items-center gap-2">
                @csrf
                <select name="status" class="input !w-44">
                    @foreach (['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance', 'unavailable' => 'Unavailable'] as $val => $label)
                        <option value="{{ $val }}" @selected($equipment->status === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Update Status</button>
            </form>
            <a href="{{ route('admin.equipment.edit', $equipment->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.equipment.destroy', $equipment->id) }}" data-confirm="Delete this equipment? This cannot be undone.">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Left: gallery + specs --}}
        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="grid gap-4 p-5 sm:grid-cols-3">
                    <div class="aspect-[4/3] overflow-hidden bg-navy-900 sm:col-span-1">
                        <img src="{{ $images[0] }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $equipment->name }}" class="h-full w-full object-cover">
                    </div>
                    <div class="sm:col-span-2">
                        <h2 class="font-display text-2xl font-bold uppercase text-navy-900">{{ $equipment->brand }} {{ $equipment->model }}</h2>
                        <p class="mt-1 text-sm text-charcoal-500">{{ $equipment->name }} · Year {{ $equipment->year }}</p>
                        <p class="mt-3 text-sm leading-relaxed text-charcoal-600">{{ $equipment->description ?: 'No description available.' }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <p class="flex justify-between border-b border-charcoal-100 pb-1.5"><span class="text-charcoal-500">Location</span><span class="font-semibold">{{ $equipment->current_location ?? '—' }}</span></p>
                            <p class="flex justify-between border-b border-charcoal-100 pb-1.5"><span class="text-charcoal-500">Region</span><span class="font-semibold">{{ $equipment->region ?? '—' }}</span></p>
                            <p class="flex justify-between border-b border-charcoal-100 pb-1.5"><span class="text-charcoal-500">Serial No.</span><span class="font-semibold">{{ $equipment->serial_number ?? '—' }}</span></p>
                            <p class="flex justify-between border-b border-charcoal-100 pb-1.5"><span class="text-charcoal-500">Condition</span><span class="font-semibold capitalize">{{ $equipment->condition }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($stats as $s)
                    <div class="card px-4 py-5 text-center">
                        <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">{{ $s[0] }}</p>
                        <p class="mt-1 font-display text-xl font-bold text-navy-900">{{ $s[1] }}</p>
                        <p class="text-xs text-charcoal-400">{{ $s[2] ?? '' }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Pricing --}}
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Rental Pricing</h3></div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-5">
                    @php
                        $prices = [
                            ['Hourly', $equipment->hourly_rate],
                            ['Daily', $equipment->daily_rate],
                            ['Weekly', $equipment->weekly_rate],
                            ['Monthly', $equipment->monthly_rate],
                            ['Deposit', $equipment->deposit],
                        ];
                    @endphp
                    @foreach ($prices as $p)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">{{ $p[0] }}</p>
                            <p class="mt-1 font-display text-lg font-bold text-brand-600">{{ $p[1] ? 'IDR ' . number_format($p[1], 0) : '—' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Maintenance history --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Maintenance History</h3>
                        <p class="text-sm text-charcoal-500">{{ $equipment->maintenanceRecords->count() }} records</p>
                    </div>
                    <a href="{{ route('admin.maintenance.create') }}" class="btn-outline btn-sm">New Record</a>
                </div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Ref</th><th>Type</th><th>Date</th><th>Cost</th><th>Downtime</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($equipment->maintenanceRecords as $m)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.maintenance.show', $m->id) }}" class="hover:text-brand-500">{{ $m->maintenance_number }}</a></td>
                                    <td>{{ ucfirst($m->type) }}</td>
                                    <td>{{ $m->date->format('d M Y') }}</td>
                                    <td>IDR {{ number_format($m->cost, 0) }}</td>
                                    <td>{{ number_format($m->downtime_hours, 1) }} h</td>
                                    <td><x-badge type="{{ $maintBadges[$m->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $m->status)) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-charcoal-400">No maintenance records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: operators --}}
        <div class="space-y-5">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Assigned Operators</h3></div>
                <div class="divide-y divide-charcoal-200">
                    @forelse ($equipment->assignedOperators as $op)
                        <div class="flex items-center gap-3 px-5 py-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-white">{{ strtoupper(substr($op->name, 0, 2)) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-navy-900">{{ $op->name }}</p>
                                <p class="text-xs text-charcoal-500">{{ $op->operator_code }} · {{ $op->certification ?? 'Certified' }}</p>
                            </div>
                            <x-badge type="{{ $op->availability === 'available' ? 'green' : ($op->availability === 'assigned' ? 'amber' : 'red') }}">{{ ucfirst(str_replace('_', ' ', $op->availability)) }}</x-badge>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-charcoal-400">No operators assigned.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Utilization</h3></div>
                <div class="p-6">
                    <p class="text-sm text-charcoal-500">Last 30 days</p>
                    <p class="mt-1 font-display text-4xl font-bold text-brand-600">{{ $equipment->utilizationRate(now()->subDays(30)->toDateString(), now()->toDateString()) }}%</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>