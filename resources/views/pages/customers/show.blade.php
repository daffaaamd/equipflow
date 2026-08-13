<x-layouts.app>
    <x-slot:title>{{ $customer->company_name }}</x-slot:title>
    <x-slot:subtitle>{{ $customer->customer_code }}</x-slot:subtitle>

    @php
        $segBadges = ['strategic' => 'purple', 'high_value' => 'red', 'medium_value' => 'amber', 'low_value' => 'blue'];
        $reqBadges = ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'];
        $conBadges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
        $invBadges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
        $prjBadges = ['planning' => 'blue', 'active' => 'green', 'completed' => 'navy', 'on_hold' => 'amber', 'cancelled' => 'red'];
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $customer->status === 'active' ? 'green' : 'red' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($customer->status) }}</x-badge>
            <x-badge type="{{ $segBadges[$customer->segment] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ $customer->segment ? str_replace('_', ' ', ucwords($customer->segment)) : 'No segment' }}</x-badge>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" data-confirm="Delete this customer?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Rental Value" :value="'IDR ' . number_format($customer->total_rental_value, 0)" accent="brand" icon="money" />
        <x-stat-card label="Active Contracts" :value="number_format($customer->active_contracts)" accent="green" icon="activity" />
        <x-stat-card label="Outstanding" :value="'IDR ' . number_format($customer->outstanding, 0)" accent="red" icon="clock" />
        <x-stat-card label="Rental Requests" :value="number_format($customer->rentalRequests->count())" accent="navy" icon="file" />
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        {{-- Info --}}
        <div class="card h-fit lg:sticky lg:top-20">
            <div class="card-header"><h3 class="text-lg font-semibold">Contact Information</h3></div>
            <div class="space-y-3 p-6 text-sm">
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Contact Person</span>{{ $customer->contact_person }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Email</span>{{ $customer->email }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Phone</span>{{ $customer->phone ?? '—' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Address</span>{{ $customer->address ?: '—' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Region</span>{{ $customer->region ?? '—' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Industry</span>{{ $customer->industry ?? '—' }}</p>
                @if ($customer->notes)
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Notes</span>{{ $customer->notes }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-5 lg:col-span-2">
            {{-- Projects --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <div><h3 class="text-lg font-semibold">Projects</h3><p class="text-sm text-charcoal-500">{{ $customer->projects->count() }} projects</p></div>
                    <a href="{{ route('admin.projects.create') }}" class="btn-outline btn-sm">New Project</a>
                </div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Project</th><th>Industry</th><th>Value</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($customer->projects as $p)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.projects.show', $p->id) }}" class="hover:text-brand-500">{{ $p->name }}</a></td>
                                    <td>{{ $p->industry }}</td>
                                    <td>IDR {{ number_format($p->contract_value, 0) }}</td>
                                    <td><x-badge type="{{ $prjBadges[$p->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No projects</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Contracts --}}
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Contracts</h3><p class="text-sm text-charcoal-500">{{ $customer->contracts->count() }} contracts</p></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Contract</th><th>Value</th><th>Period</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($customer->contracts as $c)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.contracts.show', $c->id) }}" class="hover:text-brand-500">{{ $c->contract_number }}</a></td>
                                    <td>IDR {{ number_format($c->contract_value, 0) }}</td>
                                    <td class="text-xs">{{ $c->start_date->format('d M Y') }} — {{ $c->end_date->format('d M Y') }}</td>
                                    <td><x-badge type="{{ $conBadges[$c->status] ?? 'gray' }}">{{ ucfirst($c->status) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No contracts</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Invoices --}}
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Invoices</h3><p class="text-sm text-charcoal-500">{{ $customer->invoices->count() }} invoices</p></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Invoice</th><th>Date</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($customer->invoices as $inv)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.invoices.show', $inv->id) }}" class="hover:text-brand-500">{{ $inv->invoice_number }}</a></td>
                                    <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                                    <td>IDR {{ number_format($inv->total, 0) }}</td>
                                    <td>IDR {{ number_format($inv->amount_paid, 0) }}</td>
                                    <td><x-badge type="{{ $invBadges[$inv->payment_status] ?? 'gray' }}">{{ ucfirst($inv->payment_status) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No invoices</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>