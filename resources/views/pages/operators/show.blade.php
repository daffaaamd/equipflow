<x-layouts.app>
    @php
        $availabilityBadges = ['available' => 'green', 'assigned' => 'blue', 'on_leave' => 'amber'];
    @endphp

    <x-slot:title>{{ $operator->name }}</x-slot:title>
    <x-slot:subtitle>{{ $operator->operator_code }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $availabilityBadges[$operator->availability] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst(str_replace('_', ' ', $operator->availability)) }}</x-badge>
            @if ($operator->status === 'active')
                <x-badge type="green" class="!text-sm !px-3 !py-1">Active</x-badge>
            @else
                <x-badge type="gray" class="!text-sm !px-3 !py-1">Inactive</x-badge>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.operators.edit', $operator->id) }}" class="btn-outline btn-md">Edit</a>
            <form method="POST" action="{{ route('admin.operators.destroy', $operator->id) }}" data-confirm="Delete this operator?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-md">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card lg:col-span-1">
            <div class="flex items-center gap-4 p-6">
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-navy-900 font-display text-2xl font-bold text-white">{{ strtoupper(substr($operator->name, 0, 1)) }}</span>
                <div>
                    <h3 class="font-display text-xl font-bold text-navy-900">{{ $operator->name }}</h3>
                    <p class="text-sm text-charcoal-500">{{ $operator->operator_code }}</p>
                </div>
            </div>
            <dl class="space-y-3 border-t border-charcoal-100 p-6 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Email</dt><dd class="text-right text-navy-900">{{ $operator->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Phone</dt><dd class="text-right text-navy-900">{{ $operator->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">License</dt><dd class="text-right text-navy-900">{{ $operator->license_number ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Experience</dt><dd class="text-right text-navy-900">{{ $operator->years_experience }} years</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-charcoal-500">Working Hours</dt><dd class="text-right text-navy-900">{{ number_format($operator->working_hours, 1) }} hrs</dd></div>
            </dl>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Certification</h3></div>
                <div class="p-6">
                    @if ($operator->certification)
                        <div class="flex items-center justify-between gap-4 border-b border-charcoal-100 pb-4">
                            <div>
                                <p class="font-semibold text-navy-900">{{ $operator->certification }}</p>
                                <p class="text-sm text-charcoal-500">
                                    Expires {{ $operator->certification_expiry?->format('d M Y') ?? '—' }}
                                    @if ($operator->certification_expiry)
                                        ({{ $operator->certification_expiry->diffForHumans() }})
                                    @endif
                                </p>
                            </div>
                            @if ($operator->isCertificationExpired())
                                <x-badge type="red">Expired</x-badge>
                            @elseif ($operator->isCertificationExpiring())
                                <x-badge type="amber">Expiring</x-badge>
                            @else
                                <x-badge type="green">Valid</x-badge>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-charcoal-400">No certification recorded.</p>
                    @endif
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="card">
                    <div class="card-header"><h3 class="text-lg font-semibold">Assigned Equipment</h3></div>
                    <div class="p-6 text-sm">
                        @if ($operator->assignedEquipment)
                            <a href="{{ route('admin.equipment.show', $operator->assignedEquipment->id) }}" class="font-semibold text-navy-900 hover:text-brand-500">{{ $operator->assignedEquipment->name }}</a>
                            <p class="text-charcoal-500">{{ $operator->assignedEquipment->equipment_code }}</p>
                        @else
                            <p class="text-charcoal-400">Not assigned to equipment.</p>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3 class="text-lg font-semibold">Project</h3></div>
                    <div class="p-6 text-sm">
                        @if ($operator->project)
                            <p class="font-semibold text-navy-900">{{ $operator->project->name }}</p>
                            <p class="text-charcoal-500">{{ $operator->project->customer?->company_name }}</p>
                        @else
                            <p class="text-charcoal-400">No project assigned.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($operator->notes)
                <div class="card p-6 text-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-charcoal-400">Notes</p>
                    <p class="mt-1 text-charcoal-700">{{ $operator->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>