<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $base = $isCustomer ? 'customer' : 'admin';
        $badges = ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'];
        $filters = $filters ?? [];
    @endphp

    <x-slot:title>{{ $isCustomer ? 'My Rental Requests' : 'Rental Requests' }}</x-slot:title>
    <x-slot:subtitle>{{ $isCustomer ? 'Track your equipment requests' : 'Manage incoming rental requests' }}</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route("$base.rental-requests.index") }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search request number…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'reviewed', 'quoted', 'approved', 'rejected', 'cancelled'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route("$base.rental-requests.index") }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route("$base.rental-requests.create") }}" class="btn-brand btn-md">+ New Request</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Request</th><th>Customer</th><th>Project</th><th>Items</th><th>Period</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($requests as $r)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $r->request_number }}</td>
                            <td>{{ $r->customer?->company_name }}</td>
                            <td class="max-w-[14rem] truncate">{{ $r->project_name }}</td>
                            <td>{{ $r->total_quantity }}</td>
                            <td class="text-xs">
                                @if ($r->earliest_start && $r->latest_end)
                                    {{ $r->earliest_start->format('d M') }} — {{ $r->latest_end->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><x-badge type="{{ $badges[$r->status] ?? 'gray' }}">{{ ucfirst($r->status) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route("$base.rental-requests.show", $r->id) }}" class="btn-outline btn-sm">View</a>
                                    @if (!$isCustomer)
                                        <a href="{{ route("$base.rental-requests.edit", $r->id) }}" class="btn-navy btn-sm">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-charcoal-400">No rental requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$requests" /></div>
    </div>
</x-layouts.app>