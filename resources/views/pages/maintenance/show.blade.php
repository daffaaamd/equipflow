<x-layouts.app>
    @php
        $typeBadges = ['preventive' => 'blue', 'corrective' => 'amber'];
        $statusBadges = ['scheduled' => 'blue', 'in_progress' => 'amber', 'completed' => 'green', 'cancelled' => 'gray'];
    @endphp

    <x-slot:title>{{ $maintenanceRecord->maintenance_number }}</x-slot:title>
    <x-slot:subtitle>{{ $maintenanceRecord->equipment?->name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $typeBadges[$maintenanceRecord->type] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($maintenanceRecord->type) }}</x-badge>
            <x-badge type="{{ $statusBadges[$maintenanceRecord->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst(str_replace('_', ' ', $maintenanceRecord->status)) }}</x-badge>
        </div>
        <div class="flex gap-2">
            <button data-print class="btn-outline btn-md">Print</button>
            <a href="{{ route('admin.maintenance.edit', $maintenanceRecord->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.maintenance.destroy', $maintenanceRecord->id) }}" data-confirm="Delete this maintenance record?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="print-area grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Work Order</h3>
            </div>
            <div class="p-6">
                <h4 class="font-display text-xl font-bold text-navy-900">{{ $maintenanceRecord->title }}</h4>
                @if ($maintenanceRecord->description)
                    <p class="mt-2 text-sm text-charcoal-700">{{ $maintenanceRecord->description }}</p>
                @endif

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Equipment</dt>
                        <dd class="mt-1 font-semibold text-navy-900">
                            <a href="{{ route('admin.equipment.show', $maintenanceRecord->equipment_id) }}" class="hover:text-brand-500">{{ $maintenanceRecord->equipment?->name }}</a>
                            <span class="block text-xs font-normal text-charcoal-500">{{ $maintenanceRecord->equipment?->equipment_code }}</span>
                        </dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Technician</dt>
                        <dd class="mt-1 font-semibold text-navy-900">{{ $maintenanceRecord->technician ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Date</dt>
                        <dd class="mt-1 font-semibold text-navy-900">{{ $maintenanceRecord->date->format('d M Y') }}</dd>
                    </div>
                    <div class="rounded-lg border border-charcoal-100 bg-charcoal-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Next Due</dt>
                        <dd class="mt-1 font-semibold text-navy-900">{{ $maintenanceRecord->next_due_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Cost</h3>
                <p class="mt-1 font-display text-3xl font-bold text-brand-600">IDR {{ number_format($maintenanceRecord->cost, 0) }}</p>
            </div>
            <div class="card p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Downtime</h3>
                <p class="mt-1 font-display text-3xl font-bold text-navy-900">{{ number_format($maintenanceRecord->downtime_hours, 1) }} hrs</p>
            </div>
            @if ($maintenanceRecord->parts_used)
                <div class="card p-6 text-sm">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Parts Used</h3>
                    <p class="mt-1 text-charcoal-700">{{ $maintenanceRecord->parts_used }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card no-print mt-5">
        <div class="card-header"><h3 class="text-lg font-semibold">Update Status</h3></div>
        <form method="POST" action="{{ route('admin.maintenance.update', $maintenanceRecord->id) }}" class="flex flex-wrap items-end gap-4 p-6">
            @csrf @method('PUT')
            <div class="w-52">
                <x-field label="Status" name="status">
                    <select name="status" class="input">
                        @foreach (['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" @selected($maintenanceRecord->status === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
            <input type="hidden" name="equipment_id" value="{{ $maintenanceRecord->equipment_id }}">
            <input type="hidden" name="type" value="{{ $maintenanceRecord->type }}">
            <input type="hidden" name="title" value="{{ $maintenanceRecord->title }}">
            <input type="hidden" name="description" value="{{ $maintenanceRecord->description }}">
            <input type="hidden" name="technician" value="{{ $maintenanceRecord->technician }}">
            <input type="hidden" name="date" value="{{ $maintenanceRecord->date->toDateString() }}">
            <input type="hidden" name="cost" value="{{ $maintenanceRecord->cost }}">
            <input type="hidden" name="downtime_hours" value="{{ $maintenanceRecord->downtime_hours }}">
            <input type="hidden" name="parts_used" value="{{ $maintenanceRecord->parts_used }}">
            <input type="hidden" name="next_due_date" value="{{ $maintenanceRecord->next_due_date?->toDateString() }}">
            <button type="submit" class="btn-navy btn-md">Update</button>
        </form>
    </div>
</x-layouts.app>