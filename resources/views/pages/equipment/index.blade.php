<x-layouts.app>
    <x-slot:title>Equipment</x-slot:title>
    <x-slot:subtitle>Manage the rental fleet</x-slot:subtitle>

    @php
        $badges = ['available' => 'green', 'rented' => 'amber', 'maintenance' => 'red', 'unavailable' => 'gray'];
        $statuses = ['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance', 'unavailable' => 'Unavailable'];
    @endphp

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.equipment.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search code, name, brand…">
                <select name="category" class="input !w-44" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($filters['category'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="brand" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand }}" @selected(($filters['brand'] ?? '') === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $val => $label)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.equipment.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.equipment.create') }}" class="btn-brand btn-md">+ Add Equipment</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Code</th><th>Equipment</th><th>Category</th><th>Status</th><th>Location</th><th>Daily Rate</th><th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipment as $eq)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $eq->equipment_code }}</td>
                            <td>
                                <p class="font-semibold text-charcoal-800">{{ $eq->name }}</p>
                                <p class="text-xs text-charcoal-500">{{ $eq->brand }} {{ $eq->model }} · {{ $eq->year }}</p>
                            </td>
                            <td>{{ $eq->category?->name }}</td>
                            <td><x-badge type="{{ $badges[$eq->status] ?? 'gray' }}">{{ $eq->status_label }}</x-badge></td>
                            <td>{{ $eq->current_location ?? '—' }}</td>
                            <td class="font-semibold">IDR {{ number_format($eq->daily_rate, 0) }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.equipment.show', $eq->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.equipment.edit', $eq->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-charcoal-400">No equipment found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$equipment" /></div>
    </div>
</x-layouts.app>