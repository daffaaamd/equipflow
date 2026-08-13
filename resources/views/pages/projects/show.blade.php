<x-layouts.app>
    <x-slot:title>{{ $project->name }}</x-slot:title>
    <x-slot:subtitle>{{ $project->project_code }}</x-slot:subtitle>

    @php
        $prjBadges = ['planning' => 'blue', 'active' => 'green', 'completed' => 'navy', 'on_hold' => 'amber', 'cancelled' => 'red'];
        $conBadges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
        $dlvBadges = ['scheduled' => 'blue', 'preparing' => 'amber', 'in_transit' => 'navy', 'delivered' => 'green', 'confirmed' => 'green'];
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $prjBadges[$project->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</x-badge>
            <x-badge type="navy" class="!text-sm !px-3 !py-1">{{ $project->industry }}</x-badge>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" data-confirm="Delete this project?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card h-fit lg:sticky lg:top-20">
            <div class="card-header"><h3 class="text-lg font-semibold">Project Overview</h3></div>
            <div class="space-y-3 p-6 text-sm">
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Customer</span><a href="{{ route('admin.customers.show', $project->customer_id) }}" class="font-semibold text-navy-900 hover:text-brand-500">{{ $project->customer?->company_name }}</a></p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Location</span>{{ $project->location ?: '—' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Region</span>{{ $project->region ?: '—' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Start Date</span>{{ $project->start_date?->format('d M Y') }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">End Date</span>{{ $project->end_date?->format('d M Y') ?? 'Ongoing' }}</p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Contract Value</span><span class="font-display text-xl font-bold text-brand-600">IDR {{ number_format($project->contract_value, 0) }}</span></p>
                <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Equipment Deployed</span><span class="font-semibold">{{ $project->equipment_count }} units</span></p>
                @if ($project->description)
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Description</span>{{ $project->description }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Contracts</h3></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Contract</th><th>Value</th><th>Period</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($project->contracts as $c)
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

            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Rental Requests</h3></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Request</th><th>Project Name</th><th>Quantity</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($project->rentalRequests as $r)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.rental-requests.show', $r->id) }}" class="hover:text-brand-500">{{ $r->request_number }}</a></td>
                                    <td>{{ $r->project_name }}</td>
                                    <td>{{ $r->total_quantity }}</td>
                                    <td><x-badge type="{{ ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'][$r->status] ?? 'gray' }}">{{ ucfirst($r->status) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No rental requests</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Deliveries</h3></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Delivery</th><th>Equipment</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($project->deliveries as $d)
                                <tr>
                                    <td class="font-semibold text-navy-900"><a href="{{ route('admin.deliveries.show', $d->id) }}" class="hover:text-brand-500">{{ $d->delivery_number }}</a></td>
                                    <td>{{ $d->equipment?->name }}</td>
                                    <td>{{ $d->delivery_date->format('d M Y') }}</td>
                                    <td><x-badge type="{{ $dlvBadges[$d->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-charcoal-400">No deliveries</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>