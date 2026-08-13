<x-layouts.app>
    <x-slot:title>Reports</x-slot:title>
    <x-slot:subtitle>Downloadable operational and financial reports</x-slot:subtitle>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($reports as $report)
            <a href="{{ route('admin.reports.show', $report['key']) }}" class="card group relative overflow-hidden p-6 transition hover:-translate-y-0.5 hover:border-brand-400">
                <div class="flex items-start justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-navy-900 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </span>
                    <svg class="h-5 w-5 text-charcoal-300 transition group-hover:translate-x-1 group-hover:text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </div>
                <h3 class="mt-4 font-display text-lg font-bold text-navy-900">{{ $report['name'] }}</h3>
                <p class="mt-1 text-sm text-charcoal-500">{{ $report['description'] }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>