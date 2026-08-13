<x-layouts.app>
    <x-slot:title>{{ $reportMeta['name'] }}</x-slot:title>
    <x-slot:subtitle>Report period {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</x-slot:subtitle>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.show', $report) }}" class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $filters['from'] ?? $from }}" class="input !w-40">
            <input type="date" name="to" value="{{ $filters['to'] ?? $to }}" class="input !w-40">
            <button type="submit" class="btn-navy btn-md">Apply</button>
            @if (count(array_filter($filters)))
                <a href="{{ route('admin.reports.show', $report) }}" class="btn-outline btn-md">Clear</a>
            @endif
        </form>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn-outline btn-md">All Reports</a>
            <a href="{{ route('admin.reports.export', array_merge(['report' => $report], $filters)) }}" class="btn-brand btn-md">Export CSV</a>
        </div>
    </div>

    <div class="print-area card">
        <div class="flex items-center justify-between border-b border-charcoal-200 px-6 py-5">
            <div>
                <h3 class="font-display text-xl font-bold text-navy-900">{{ $reportMeta['name'] }}</h3>
                <p class="text-sm text-charcoal-500">EquipFlow · {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </div>
            <span class="hidden items-center gap-2 sm:flex">
                <span class="flex h-9 w-9 items-center justify-center bg-brand-500 font-display text-lg font-bold text-white">E</span>
                <span class="font-display text-lg font-bold uppercase text-navy-900">EquipFlow</span>
            </span>
        </div>
        <div class="table-wrap">
            <table class="table-base">
                <thead>
                    <tr>
                        @foreach ($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) }}" class="px-4 py-10 text-center text-charcoal-400">No data in the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>