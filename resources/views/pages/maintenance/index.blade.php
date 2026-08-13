<x-layouts.app>
    @php
        $typeBadges = ['preventive' => 'blue', 'corrective' => 'amber'];
        $statusBadges = ['scheduled' => 'blue', 'in_progress' => 'amber', 'completed' => 'green', 'cancelled' => 'gray'];
    @endphp

    <x-slot:title>Maintenance</x-slot:title>
    <x-slot:subtitle>Preventive and corrective maintenance records</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.maintenance.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search maintenance number…">
                <select name="equipment_id" class="input !w-48" onchange="this.form.submit()">
                    <option value="">All Equipment</option>
                    @foreach ($equipment as $eq)
                        <option value="{{ $eq->id }}" @selected(($filters['equipment_id'] ?? '') == $eq->id)>{{ $eq->equipment_code }}</option>
                    @endforeach
                </select>
                <select name="type" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach (['preventive', 'corrective'] as $val)
                        <option value="{{ $val }}" @selected(($filters['type'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.maintenance.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.maintenance.create') }}" class="btn-brand btn-md">+ Schedule Maintenance</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Maintenance</th><th>Equipment</th><th>Type</th><th>Date</th><th>Cost</th><th>Downtime</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($records as $r)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $r->maintenance_number }}</td>
                            <td>
                                <span class="font-semibold text-navy-900">{{ $r->equipment?->name }}</span>
                                <span class="block text-xs text-charcoal-500">{{ $r->equipment?->equipment_code }}</span>
                            </td>
                            <td><x-badge type="{{ $typeBadges[$r->type] ?? 'gray' }}">{{ ucfirst($r->type) }}</x-badge></td>
                            <td>{{ $r->date->format('d M Y') }}</td>
                            <td>IDR {{ number_format($r->cost, 0) }}</td>
                            <td>{{ $r->downtime_hours }} hrs</td>
                            <td><x-badge type="{{ $statusBadges[$r->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $r->status)) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.maintenance.show', $r->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.maintenance.edit', $r->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-charcoal-400">No maintenance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$records" /></div>
    </div>
</x-layouts.app>