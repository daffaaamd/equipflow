<x-layouts.landing>
    @php
        $statusBadges = ['planning' => 'blue', 'active' => 'green', 'completed' => 'navy', 'on_hold' => 'amber', 'cancelled' => 'red'];
        $heroImgs = [
            '/img/categories/hydraulic-excavator.jpg',
            '/img/categories/articulated-dump-truck.jpg',
            '/img/categories/crawler-crane.jpg',
            '/img/categories/asphalt-paver.jpg',
            '/img/categories/bulldozer.jpg',
            '/img/categories/concrete-pump.jpg',
        ];
    @endphp

    <section class="bg-charcoal-100 py-16">
        <div class="container-equip">
            <div class="reveal max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Portfolio</p>
                <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-6xl">Projects We Power</h1>
                <p class="mt-5 text-lg text-charcoal-600">
                    From high-rise foundations to regional infrastructure programs, our fleet is deployed on
                    projects that shape communities.
                </p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $i => $project)
                    <article class="reveal card group overflow-hidden" style="transition-delay:{{ ($i % 3) * 0.06 }}s">
                        <div class="img-reveal relative aspect-[16/9] overflow-hidden bg-navy-900">
                            <img src="{{ $heroImgs[$i % 3] }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute left-4 top-4"><x-badge type="{{ $statusBadges[$project->status] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</x-badge></div>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600">{{ $project->industry }}</p>
                            <h3 class="mt-2 font-display text-xl font-bold uppercase leading-tight text-navy-900">{{ $project->name }}</h3>
                            <p class="mt-1 text-sm text-charcoal-500">{{ $project->customer->company_name }}</p>
                            <div class="mt-4 flex items-center justify-between border-t border-charcoal-100 pt-4 text-sm">
                                <span class="flex items-center gap-1.5 text-charcoal-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    {{ $project->city ?? $project->region ?? 'Indonesia' }}
                                </span>
                                <span class="font-semibold text-charcoal-800">IDR {{ number_format($project->contract_value, 0) }}</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full"><x-empty-state title="No projects yet" /></div>
                @endforelse
            </div>

            <x-pagination :links="$projects" />
        </div>
    </section>
</x-layouts.landing>