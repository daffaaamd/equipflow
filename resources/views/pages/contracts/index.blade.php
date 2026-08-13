<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $base = $isCustomer ? 'customer' : 'admin';
        $badges = ['draft' => 'gray', 'active' => 'green', 'completed' => 'navy', 'terminated' => 'red'];
    @endphp

    <x-slot:title>{{ $isCustomer ? 'My Contracts' : 'Contracts' }}</x-slot:title>
    <x-slot:subtitle>{{ $isCustomer ? 'View signed rental agreements' : 'Manage rental contracts' }}</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route("$base.contracts.index") }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search contract number…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['draft', 'active', 'completed', 'terminated'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route("$base.contracts.index") }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            @if (!$isCustomer)
                <a href="{{ route('admin.contracts.create') }}" class="btn-brand btn-md">+ New Contract</a>
            @endif
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Contract</th><th>Customer</th><th>Value</th><th>Period</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($contracts as $c)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $c->contract_number }}</td>
                            <td>{{ $c->customer?->company_name }}</td>
                            <td class="font-semibold">IDR {{ number_format($c->contract_value, 0) }}</td>
                            <td class="text-xs">{{ $c->start_date->format('d M Y') }} — {{ $c->end_date->format('d M Y') }}</td>
                            <td><x-badge type="{{ $badges[$c->status] ?? 'gray' }}">{{ ucfirst($c->status) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route("$base.contracts.show", $c->id) }}" class="btn-outline btn-sm">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-charcoal-400">No contracts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$contracts" /></div>
    </div>
</x-layouts.app>