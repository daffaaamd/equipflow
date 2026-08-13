<x-layouts.app>
    <x-slot:title>Customers</x-slot:title>
    <x-slot:subtitle>Manage customer accounts</x-slot:subtitle>

    @php
        $segBadges = ['strategic' => 'purple', 'high_value' => 'red', 'medium_value' => 'amber', 'low_value' => 'blue'];
    @endphp

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-64" placeholder="Search company, code, contact…">
                <select name="segment" class="input !w-40" onchange="this.form.submit()">
                    <option value="">All Segments</option>
                    @foreach (['strategic' => 'Strategic', 'high_value' => 'High Value', 'medium_value' => 'Medium Value', 'low_value' => 'Low Value'] as $val => $label)
                        <option value="{{ $val }}" @selected(($filters['segment'] ?? '') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="input !w-32" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.customers.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.customers.create') }}" class="btn-brand btn-md">+ Add Customer</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Customer</th><th>Contact</th><th>Segment</th><th>Active Contracts</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($customers as $c)
                        <tr>
                            <td>
                                <p class="font-semibold text-navy-900">{{ $c->company_name }}</p>
                                <p class="text-xs text-charcoal-500">{{ $c->customer_code }}</p>
                            </td>
                            <td>
                                <p class="text-sm">{{ $c->contact_person }}</p>
                                <p class="text-xs text-charcoal-500">{{ $c->email }}</p>
                            </td>
                            <td><x-badge type="{{ $segBadges[$c->segment] ?? 'gray' }}">{{ $c->segment ? str_replace('_', ' ', ucwords($c->segment)) : '—' }}</x-badge></td>
                            <td class="font-semibold">{{ $c->contracts_count }}</td>
                            <td><x-badge type="{{ $c->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($c->status) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.customers.show', $c->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.customers.edit', $c->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-charcoal-400">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$customers" /></div>
    </div>
</x-layouts.app>