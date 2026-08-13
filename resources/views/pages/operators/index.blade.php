<x-layouts.app>
    @php
        $availabilityBadges = ['available' => 'green', 'assigned' => 'blue', 'on_leave' => 'amber'];
        $statusBadges = ['active' => 'green', 'inactive' => 'gray'];
    @endphp

    <x-slot:title>Operators</x-slot:title>
    <x-slot:subtitle>Manage equipment operators and certifications</x-slot:subtitle>

    @if ($certificationExpiring)
        <div class="mb-5 flex items-start gap-3 border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <span class="mt-0.5 text-lg leading-none">!</span>
            <p><strong>{{ $certificationExpiring }} operator{{ $certificationExpiring > 1 ? 's' : '' }}</strong> with certification expiring within 60 days.</p>
        </div>
    @endif

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.operators.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search name / code…">
                <select name="availability" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Availability</option>
                    @foreach (['available', 'assigned', 'on_leave'] as $val)
                        <option value="{{ $val }}" @selected(($filters['availability'] ?? '') === $val)>{{ ucfirst(str_replace('_', ' ', $val)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.operators.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.operators.create') }}" class="btn-brand btn-md">+ New Operator</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Operator</th><th>Assigned Equipment</th><th>Project</th><th>Experience</th><th>Certification</th><th>Availability</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($operators as $op)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy-900 font-display text-sm font-bold text-white">{{ strtoupper(substr($op->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="font-semibold text-navy-900">{{ $op->name }}</p>
                                        <p class="text-xs text-charcoal-500">{{ $op->operator_code }} · {{ $op->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $op->assignedEquipment?->name ?? '—' }}</td>
                            <td class="max-w-[12rem] truncate">{{ $op->project?->name ?? '—' }}</td>
                            <td>{{ $op->years_experience }} yrs</td>
                            <td>
                                <div class="max-w-[10rem] truncate text-xs">
                                    {{ $op->certification ?? '—' }}
                                    @if ($op->certification_expiry)
                                        <span class="block {{ $op->isCertificationExpired() ? 'text-red-600' : ($op->isCertificationExpiring() ? 'text-amber-600' : 'text-charcoal-400') }}">
                                            {{ $op->certification_expiry->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td><x-badge type="{{ $availabilityBadges[$op->availability] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $op->availability)) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.operators.show', $op->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.operators.edit', $op->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-charcoal-400">No operators found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$operators" /></div>
    </div>
</x-layouts.app>