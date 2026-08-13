<x-layouts.landing>
    @php
        $images = $equipment->images->isNotEmpty()
            ? $equipment->images->map(fn ($i) => $i->url)->all()
            : ['/img/placeholder.svg'];
        $statusBadges = ['available' => 'green', 'rented' => 'amber', 'maintenance' => 'red', 'unavailable' => 'gray'];

        $specs = [
            'Brand' => $equipment->brand,
            'Model' => $equipment->model,
            'Year' => $equipment->year,
            'Serial Number' => $equipment->serial_number ?? '-',
            'Operating Weight' => $equipment->operating_weight ? number_format($equipment->operating_weight, 0) . ' kg' : '-',
            'Engine Power' => $equipment->engine_power ? number_format($equipment->engine_power, 0) . ' hp' : '-',
            'Bucket Capacity' => $equipment->bucket_capacity ? number_format($equipment->bucket_capacity, 2) . ' m³' : '-',
            'Fuel Capacity' => $equipment->fuel_capacity ? number_format($equipment->fuel_capacity, 0) . ' L' : '-',
            'Operating Hours' => number_format($equipment->operating_hours, 0) . ' hrs',
            'Condition' => ucfirst($equipment->condition),
            'Location' => $equipment->current_location ?? '-',
            'Region' => $equipment->region ?? '-',
        ];
    @endphp

    <section class="bg-charcoal-100 py-10">
        <div class="container-equip">
            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 text-sm text-charcoal-500">
                <a href="{{ route('landing') }}" class="hover:text-navy-900">Home</a> <span>/</span>
                <a href="{{ route('catalog') }}" class="hover:text-navy-900">Equipment</a> <span>/</span>
                <span class="text-navy-900">{{ $equipment->equipment_code }}</span>
            </nav>

            <div class="grid gap-10 lg:grid-cols-[1.2fr_1fr]">
                {{-- Gallery --}}
                <div x-data="{ active: 0, images: {{ \Illuminate\Support\Js::from($images) }} }">
                    <div class="img-reveal is-visible relative aspect-[16/10] overflow-hidden bg-navy-900">
                        <img :src="images[active] || '{{ $images[0] ?? '/img/placeholder.svg' }}'" src="{{ $images[0] ?? '/img/placeholder.svg' }}"
                             onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $equipment->name }}" class="h-full w-full object-cover">
                        <div class="absolute left-4 top-4 flex gap-2">
                            <x-badge type="{{ $statusBadges[$equipment->status] ?? 'gray' }}">{{ $equipment->status_label }}</x-badge>
                            <x-badge type="outline" class="!bg-white/90 backdrop-blur">{{ $equipment->category->name }}</x-badge>
                        </div>
                    </div>
                    @if (count($images) > 1)
                        <div class="mt-3 grid grid-cols-5 gap-2">
                            @foreach ($images as $i => $img)
                                <button type="button" x-on:click="active = {{ $i }}"
                                        class="img-reveal is-visible aspect-[4/3] overflow-hidden border-2"
                                        :class="active === {{ $i }} ? 'border-brand-500' : 'border-transparent'">
                                    <img src="{{ $img }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="Thumbnail" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Summary --}}
                <div class="reveal">
                    <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">{{ $equipment->equipment_code }} · {{ $equipment->category->name }}</p>
                    <h1 class="mt-2 font-display text-4xl font-bold uppercase leading-tight tracking-tight text-navy-900 sm:text-5xl">{{ $equipment->name }}</h1>
                    <p class="mt-1 text-lg font-semibold text-charcoal-600">{{ $equipment->brand }} {{ $equipment->model }} ({{ $equipment->year }})</p>

                    <div class="mt-6 border border-charcoal-200 bg-white p-6">
                        <div class="flex flex-col items-start gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-500">Daily Rate</p>
                                <p class="font-display text-4xl font-bold text-brand-600">IDR {{ number_format($equipment->daily_rate) }}</p>
                            </div>
                            <p class="text-xs text-charcoal-500">excl. operator &amp; transport</p>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-charcoal-200 pt-5 text-sm">
                            @if ($equipment->weekly_rate)
                                <div><p class="text-xs uppercase tracking-widest text-charcoal-400">Weekly</p><p class="font-semibold text-charcoal-800">IDR {{ number_format($equipment->weekly_rate) }}</p></div>
                            @endif
                            @if ($equipment->monthly_rate)
                                <div><p class="text-xs uppercase tracking-widest text-charcoal-400">Monthly</p><p class="font-semibold text-charcoal-800">IDR {{ number_format($equipment->monthly_rate) }}</p></div>
                            @endif
                            @if ($equipment->hourly_rate)
                                <div><p class="text-xs uppercase tracking-widest text-charcoal-400">Hourly</p><p class="font-semibold text-charcoal-800">IDR {{ number_format($equipment->hourly_rate) }}</p></div>
                            @endif
                            @if ($equipment->deposit)
                                <div><p class="text-xs uppercase tracking-widest text-charcoal-400">Deposit</p><p class="font-semibold text-charcoal-800">IDR {{ number_format($equipment->deposit) }}</p></div>
                            @endif
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('quote.create', ['equipment' => $equipment->id]) }}" class="btn-brand btn-lg">Request This Equipment</a>
                            <a href="{{ route('quote.create') }}" class="btn-outline-navy btn-lg">Build a Custom Quote</a>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                        @php
                            $tiny = [
                                ['Ops Hours', number_format($equipment->operating_hours, 0), 'hours'],
                                ['Next Service', $equipment->next_service_hours ? number_format(max(0, (float) ($equipment->next_service_hours - $equipment->operating_hours)), 0) . ' hrs' : '—', 'remaining'],
                                ['Condition', ucfirst($equipment->condition), 'inspected'],
                            ];
                        @endphp
                        @foreach ($tiny as $t)
                            <div class="border border-charcoal-200 bg-white px-3 py-4">
                                <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">{{ $t[0] }}</p>
                                <p class="mt-1 font-display text-lg font-bold text-navy-900">{{ $t[1] }}</p>
                                <p class="text-xs text-charcoal-400">{{ $t[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tabs: Specs / Description / Support --}}
            <div x-data="{ tab: 'specs' }" class="reveal mt-12">
                <div class="flex flex-wrap gap-1 border-b border-charcoal-200">
                    @php
                        $tabs = ['specs' => 'Technical Specifications', 'description' => 'About This Unit', 'support' => 'Operator & Support'];
                    @endphp
                    @foreach ($tabs as $key => $label)
                        <button type="button" x-on:click="tab = '{{ $key }}'"
                                class="border-b-2 px-5 py-3 text-sm font-semibold uppercase tracking-wide transition-colors"
                                :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-600' : 'border-transparent text-charcoal-500 hover:text-navy-900'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="bg-white p-6 sm:p-8">
                    <div x-show="tab === 'specs'">
                        <dl class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
                            @foreach ($specs as $label => $value)
                                <div class="flex justify-between gap-4 border-b border-charcoal-100 pb-3">
                                    <dt class="text-sm font-semibold uppercase tracking-wide text-charcoal-500">{{ $label }}</dt>
                                    <dd class="text-sm font-semibold text-charcoal-800">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    <div x-show="tab === 'description'" x-cloak>
                        <p class="max-w-3xl text-charcoal-700 leading-relaxed">
                            {{ $equipment->description ?: "This {$equipment->category->name} is part of the EquipFlow fleet, maintained to manufacturer specifications and inspected before every deployment." }}
                        </p>
                    </div>

                    <div x-show="tab === 'support'" x-cloak>
                        <div class="grid gap-6 sm:grid-cols-3">
                            @forelse ($equipment->assignedOperators as $op)
                                <div class="border border-charcoal-200 p-5">
                                    <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">Assigned Operator</p>
                                    <p class="mt-2 font-display text-lg font-bold text-navy-900">{{ $op->name }}</p>
                                    <p class="text-sm text-charcoal-500">{{ $op->certification ?? 'Certified' }} · {{ $op->years_experience ?? 0 }} yrs</p>
                                    <x-badge type="{{ $op->isCertificationExpiring() ? 'amber' : 'green' }}" class="mt-3">{{ $op->isCertificationExpiring() ? 'Cert. expiring soon' : 'Cert. valid' }}</x-badge>
                                </div>
                            @empty
                                <p class="text-sm text-charcoal-500 sm:col-span-3">Operators can be provided on request for this unit.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maintenance history --}}
            @if ($equipment->maintenanceRecords->isNotEmpty())
                <div class="reveal mt-10">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold">Maintenance History</h3>
                            <p class="text-sm text-charcoal-500">Recent service records for this unit</p>
                        </div>
                        <div class="table-wrap">
                            <table class="table-base">
                                <thead><tr><th>Ref</th><th>Type</th><th>Date</th><th>Cost</th><th>Downtime</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach ($equipment->maintenanceRecords->take(8) as $m)
                                        <tr>
                                            <td class="font-semibold text-navy-900">{{ $m->maintenance_number }}</td>
                                            <td>{{ ucfirst($m->type) }}</td>
                                            <td>{{ $m->date->format('d M Y') }}</td>
                                            <td>IDR {{ number_format($m->cost) }}</td>
                                            <td>{{ number_format($m->downtime_hours, 1) }} h</td>
                                            <td><x-badge type="{{ $m->status === 'completed' ? 'green' : ($m->status === 'cancelled' ? 'gray' : 'amber') }}">{{ ucfirst(str_replace('_', ' ', $m->status)) }}</x-badge></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Similar --}}
            @if ($similar->isNotEmpty())
                <div class="reveal mt-14">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-navy-900">Similar Equipment</h2>
                        <a href="{{ route('catalog') }}" class="btn-outline-navy btn-md">View All</a>
                    </div>
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($similar as $s)
                            <a href="{{ route('catalog.show', $s->id) }}" class="card group overflow-hidden">
                                <div class="img-reveal relative aspect-[4/3] overflow-hidden bg-navy-900">
                                    <img src="{{ $s->primaryImage()?->url ?? '/img/placeholder.svg' }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $s->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <div class="absolute left-3 top-3"><x-badge type="{{ $statusBadges[$s->status] ?? 'gray' }}">{{ $s->status_label }}</x-badge></div>
                                </div>
                                <div class="p-4">
                                    <p class="font-display text-base font-bold uppercase text-navy-900">{{ $s->brand }} {{ $s->model }}</p>
                                    <p class="text-sm text-charcoal-500">IDR {{ number_format($s->daily_rate) }}/day</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layouts.landing>
