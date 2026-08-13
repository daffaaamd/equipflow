<x-layouts.app>
    <x-slot:title>Projects</x-slot:title>
    <x-slot:subtitle>Manage project portfolio</x-slot:subtitle>

    @php
        $prjBadges = ['planning' => 'blue', 'active' => 'green', 'completed' => 'navy', 'on_hold' => 'amber', 'cancelled' => 'red'];
    @endphp

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-64" placeholder="Search project, code…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['planning', 'active', 'completed', 'on_hold', 'cancelled'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst(str_replace('_', ' ', $val)) }}</option>
                    @endforeach
                </select>
                <input type="text" name="industry" value="{{ $filters['industry'] ?? '' }}" class="input !w-40" placeholder="Industry…">
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.projects.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.projects.create') }}" class="btn-brand btn-md">+ Add Project</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Project</th><th>Customer</th><th>Industry</th><th>Region</th><th>Value</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($projects as $p)
                        <tr>
                            <td>
                                <p class="font-semibold text-navy-900">{{ $p->name }}</p>
                                <p class="text-xs text-charcoal-500">{{ $p->project_code }}</p>
                            </td>
                            <td>{{ $p->customer?->company_name }}</td>
                            <td>{{ $p->industry }}</td>
                            <td>{{ $p->region ?? '—' }}</td>
                            <td class="font-semibold">IDR {{ number_format($p->contract_value, 0) }}</td>
                            <td><x-badge type="{{ $prjBadges[$p->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.projects.show', $p->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.projects.edit', $p->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-charcoal-400">No projects found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$projects" /></div>
    </div>
</x-layouts.app>