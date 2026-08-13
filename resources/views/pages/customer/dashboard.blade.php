<x-layouts.app>
    @php
        $reqBadges = ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'];
        $conBadges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
        $invBadges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
        $dlvBadges = ['scheduled' => 'blue', 'preparing' => 'amber', 'in_transit' => 'navy', 'delivered' => 'green', 'confirmed' => 'green'];
    @endphp

    <x-slot:title>{{ $customer ? 'Welcome, ' . $customer->contact_person : 'Customer Portal' }}</x-slot:title>
    <x-slot:subtitle>{{ $customer?->company_name ?? '' }}</x-slot:subtitle>

    {{-- KPI --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Active Rentals" :value="number_format($activeRentals)" accent="brand"
                      :icon="'<path d=\"M11 17l2 2a1 1 0 002 0l3-3a1 1 0 000-1.4l-4-4a1 1 0 00-1.4 0l-3 3a1 1 0 000 1.4L11 17z\"/>" />
        <x-stat-card label="Pending Requests" :value="number_format($pendingRequests)" accent="navy"
                      :icon="'<path d=\"M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z\"/><path d=\"M14 3v6h6\"/>'" />
        <x-stat-card label="Active Projects" :value="number_format($activeProjects)" accent="blue"
                      :icon="'<rect x=\"3\" y=\"7\" width=\"18\" height=\"13\" rx=\"1\"/><path d=\"M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18\"/>'" />
        <x-stat-card label="Outstanding Balance" :value="'IDR ' . number_format($outstanding, 0)" accent="red"
                      :icon="'<rect x=\"2\" y=\"6\" width=\"20\" height=\"12\" rx=\"1\"/><circle cx=\"12\" cy=\"12\" r=\"2.5\"/>'" />
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        {{-- Rental history --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Rental History</h3>
                    <p class="text-sm text-charcoal-500">Your recent contracts</p>
                </div>
                <a href="{{ route('customer.contracts.index') }}" class="btn-outline btn-sm">All contracts</a>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Contract</th><th>Value</th><th>Period</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($rentalHistory as $c)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('customer.contracts.show', $c->id) }}" class="hover:text-brand-500">{{ $c->contract_number }}</a></td>
                                <td class="font-semibold">IDR {{ number_format($c->contract_value, 0) }}</td>
                                <td class="text-xs">{{ $c->start_date->format('d M Y') }} — {{ $c->end_date->format('d M Y') }}</td>
                                <td><x-badge type="{{ $conBadges[$c->status] ?? 'gray' }}">{{ ucfirst($c->status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No contracts yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Upcoming deliveries --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Upcoming Deliveries</h3>
                <p class="text-sm text-charcoal-500">Scheduled equipment deliveries</p>
            </div>
            <div class="divide-y divide-charcoal-200">
                @forelse ($upcomingDeliveries as $d)
                    <div class="flex items-center gap-4 px-5 py-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-navy-100 text-navy-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M1 4h13v12H1z"/><path d="M14 8h4l3 4v4h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-navy-900">{{ $d->equipment?->name ?? 'Equipment' }} — {{ $d->delivery_number }}</p>
                            <p class="text-xs text-charcoal-500">{{ $d->delivery_date->format('d M Y') }} · {{ $d->destination ?? '—' }}</p>
                        </div>
                        <x-badge type="{{ $dlvBadges[$d->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</x-badge>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-charcoal-400">No upcoming deliveries</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        {{-- Recent requests --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Recent Rental Requests</h3>
                    <p class="text-sm text-charcoal-500">Track your request status</p>
                </div>
                <a href="{{ route('customer.rental-requests.create') }}" class="btn-brand btn-sm">New Request</a>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Request</th><th>Project</th><th>Items</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentRequests as $r)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('customer.rental-requests.show', $r->id) }}" class="hover:text-brand-500">{{ $r->request_number }}</a></td>
                                <td class="max-w-[12rem] truncate">{{ $r->project_name }}</td>
                                <td>{{ $r->total_quantity }}</td>
                                <td><x-badge type="{{ $reqBadges[$r->status] ?? 'gray' }}">{{ ucfirst($r->status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No requests yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent invoices --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Recent Invoices</h3>
                    <p class="text-sm text-charcoal-500">Latest billing</p>
                </div>
                <a href="{{ route('customer.invoices.index') }}" class="btn-outline btn-sm">All invoices</a>
            </div>
            <div class="table-wrap">
                <table class="table-base">
                    <thead><tr><th>Invoice</th><th>Date</th><th>Due</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($invoices as $i)
                            <tr>
                                <td class="font-semibold text-navy-900"><a href="{{ route('customer.invoices.show', $i->id) }}" class="hover:text-brand-500">{{ $i->invoice_number }}</a></td>
                                <td>{{ $i->invoice_date->format('d M Y') }}</td>
                                <td>{{ $i->due_date->format('d M Y') }}</td>
                                <td class="font-semibold">IDR {{ number_format($i->total, 0) }}</td>
                                <td><x-badge type="{{ $invBadges[$i->payment_status] ?? 'gray' }}">{{ ucfirst($i->payment_status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No invoices yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>