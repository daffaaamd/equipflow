<x-layouts.app>
    @php
        $badges = ['scheduled' => 'blue', 'preparing' => 'amber', 'in_transit' => 'navy', 'delivered' => 'green', 'confirmed' => 'green'];
    @endphp

    <x-slot:title>Deliveries</x-slot:title>
    <x-slot:subtitle>Schedule and track equipment delivery</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.deliveries.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search delivery number…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['scheduled', 'preparing', 'in_transit', 'delivered', 'confirmed'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst(str_replace('_', ' ', $val)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.deliveries.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.deliveries.create') }}" class="btn-brand btn-md">+ Schedule Delivery</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Delivery</th><th>Equipment</th><th>Destination</th><th>Date</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($deliveries as $d)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $d->delivery_number }}</td>
                            <td>{{ $d->equipment?->name }}</td>
                            <td class="max-w-[14rem] truncate">{{ $d->destination ?? '—' }}</td>
                            <td>{{ $d->delivery_date->format('d M Y') }}</td>
                            <td><x-badge type="{{ $badges[$d->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.deliveries.show', $d->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.deliveries.edit', $d->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-charcoal-400">No deliveries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$deliveries" /></div>
    </div>
</x-layouts.app>