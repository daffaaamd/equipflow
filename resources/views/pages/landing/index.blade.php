<x-layouts.landing>
    @php
        $hero = [
            'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789',
            'https://images.unsplash.com/photo-1541888946425-d81bb19240f5',
            'https://images.unsplash.com/photo-1581092160562-40aa08e788b0',
        ][1];
    @endphp

    {{-- Hero --}}
    <section class="relative flex min-h-[88vh] items-center overflow-hidden bg-navy-950">
        <div class="absolute inset-0">
            <img src="{{ $hero }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="Heavy equipment at a construction site"
                 class="hero-zoom h-full w-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/80 to-navy-950/30"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-navy-950 to-transparent"></div>
        </div>

        <div class="container-equip relative py-24">
            <div class="max-w-3xl">
                <p class="reveal flex items-center gap-3 text-xs font-bold uppercase tracking-[0.3em] text-brand-400">
                    <span class="h-px w-10 bg-brand-500"></span>
                    Heavy Equipment Rental &amp; Fleet Management
                </p>
                <h1 class="reveal mt-6 font-display text-4xl font-bold uppercase leading-[0.95] tracking-tight text-white sm:text-6xl lg:text-7xl">
                    Power Your Project.<br>
                    <span class="text-brand-500">We Move the Earth.</span>
                </h1>
                <p class="reveal mt-6 max-w-xl text-base leading-relaxed text-charcoal-200 sm:text-lg" style="transition-delay:.1s">
                    A modern fleet of excavators, dozers, cranes, and haul trucks — ready to deploy across
                    construction, mining, infrastructure, and industrial sites. Transparent pricing, guaranteed
                    availability, and certified operators.
                </p>
                <div class="reveal mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4" style="transition-delay:.2s">
                    <a href="{{ route('catalog') }}" class="btn-brand btn-lg w-full justify-center sm:w-auto">Browse the Fleet</a>
                    <a href="{{ route('quote.create') }}" class="btn-lg w-full justify-center border border-white/30 text-white hover:border-brand-500 hover:bg-brand-500 sm:w-auto">Request a Quote</a>
                </div>
            </div>

            {{-- Hero stats --}}
            <div class="reveal mt-16 grid max-w-4xl grid-cols-2 gap-px overflow-hidden border border-white/10 bg-white/10 sm:grid-cols-4" style="transition-delay:.3s">
                <div class="bg-navy-950/70 px-6 py-5 backdrop-blur">
                    <p class="font-display text-3xl font-bold text-white" data-counter="{{ $stats['equipment'] }}">{{ $stats['equipment'] }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-charcoal-400">Fleet Units</p>
                </div>
                <div class="bg-navy-950/70 px-6 py-5 backdrop-blur">
                    <p class="font-display text-3xl font-bold text-brand-500" data-counter="{{ $stats['projects'] }}">{{ $stats['projects'] }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-charcoal-400">Projects Served</p>
                </div>
                <div class="bg-navy-950/70 px-6 py-5 backdrop-blur">
                    <p class="font-display text-3xl font-bold text-white" data-counter="{{ $stats['categories'] }}">{{ $stats['categories'] }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-charcoal-400">Equipment Types</p>
                </div>
                <div class="bg-navy-950/70 px-6 py-5 backdrop-blur">
                    <p class="font-display text-3xl font-bold text-white" data-counter="{{ $stats['transactions'] }}">{{ $stats['transactions'] }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-charcoal-400">Contracts Signed</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="bg-white py-20">
        <div class="container-equip">
            <div class="reveal flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">The Fleet</p>
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-5xl">Equipment Categories</h2>
                </div>
                <a href="{{ route('catalog') }}" class="btn-outline-navy btn-md">View Full Catalog →</a>
            </div>

            <div class="mt-12 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @forelse ($categories as $cat)
                    <a href="{{ route('catalog', ['category' => $cat->id]) }}"
                       class="img-reveal group relative block overflow-hidden bg-navy-900">
                        <img src="{{ $cat->url }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'"
                             alt="{{ $cat->name }}" class="h-48 w-full object-cover opacity-60 transition-opacity group-hover:opacity-40 sm:h-56">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5">
                            <h3 class="font-display text-xl font-bold uppercase tracking-wide text-white sm:text-2xl">{{ $cat->name }}</h3>
                            <p class="mt-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-brand-400">
                                {{ $cat->equipment_count }} unit{{ $cat->equipment_count == 1 ? '' : 's' }}
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full"><x-empty-state title="No categories" /></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Featured equipment --}}
    <section class="bg-charcoal-100 py-20">
        <div class="container-equip">
            <div class="reveal flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Ready to Deploy</p>
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-5xl">Featured Equipment</h2>
                </div>
                <a href="{{ route('catalog') }}" class="btn-brand btn-md">Explore All →</a>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($featured as $eq)
                    <article class="card reveal group flex flex-col overflow-hidden">
                        <div class="img-reveal relative aspect-[4/3] overflow-hidden bg-navy-900">
                            <img src="{{ $eq->primaryImage()?->url ?? '/img/placeholder.svg' }}"
                                 onerror="this.onerror=null;this.src='/img/placeholder.svg'"
                                 alt="{{ $eq->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute left-3 top-3 flex gap-2">
                                <x-badge type="{{ $eq->status === 'available' ? 'green' : ($eq->status === 'rented' ? 'amber' : 'red') }}">{{ $eq->status_label }}</x-badge>
                            </div>
                            <div class="absolute right-3 top-3">
                                <x-badge type="outline" class="!bg-white/90 backdrop-blur">{{ $eq->category->name }}</x-badge>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-display text-xl font-bold uppercase leading-tight text-navy-900">{{ $eq->name }}</h3>
                                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-widest text-charcoal-500">{{ $eq->brand }} {{ $eq->model }} · {{ $eq->equipment_code }}</p>
                                </div>
                            </div>
                            <p class="mt-3 flex items-center gap-1.5 text-sm text-charcoal-500">
                                <svg class="h-4 w-4 text-charcoal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                {{ $eq->current_location ?? 'Jakarta, Indonesia' }}
                            </p>
                            <div class="mt-4 flex flex-col items-start gap-3 border-t border-charcoal-200 pt-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-500">From</p>
                                    <p class="font-display text-2xl font-bold text-brand-600">IDR {{ number_format($eq->daily_rate) }}<span class="text-sm text-charcoal-500">/day</span></p>
                                </div>
                                <a href="{{ route('catalog.show', $eq->id) }}" class="btn-outline-navy btn-sm">View Details</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full"><x-empty-state title="No equipment" /></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Solutions --}}
    <section class="bg-navy-950 py-20">
        <div class="container-equip">
            <div class="reveal max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Industry Solutions</p>
                <h2 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-white sm:text-5xl">Built for the Toughest Industries</h2>
                <p class="mt-4 text-charcoal-300">From urban construction sites to remote mining operations, EquipFlow delivers the right machine at the right time — with complete support.</p>
            </div>

            <div class="mt-12 grid gap-px overflow-hidden border border-navy-800 bg-navy-800 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($solutions as $i => $sol)
                    <div class="reveal group bg-navy-950 p-7 transition-colors hover:bg-navy-900" style="transition-delay:{{ $i * 0.05 }}s">
                        <div class="flex h-11 w-11 items-center justify-center bg-brand-500/15">
                            <span class="font-display text-2xl font-bold text-brand-500">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="mt-5 font-display text-2xl font-bold uppercase tracking-wide text-white">{{ $sol['title'] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-charcoal-300">{{ $sol['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Projects --}}
    <section class="bg-white py-20">
        <div class="container-equip">
            <div class="reveal flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Recent Work</p>
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-5xl">Delivered Projects</h2>
                </div>
                <a href="{{ route('projects') }}" class="btn-outline-navy btn-md">All Projects →</a>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @forelse ($projects as $project)
                    <article class="card reveal group border-t-4 border-t-brand-500 p-6 transition-shadow hover:shadow-md">
                        <p class="text-xs font-semibold uppercase tracking-widest text-brand-600">{{ $project->industry }}</p>
                        <h3 class="mt-3 font-display text-xl font-bold uppercase leading-tight text-navy-900">{{ $project->name }}</h3>
                        <p class="mt-2 text-sm text-charcoal-500">
                            {{ $project->customer->company_name }}<br>
                            {{ $project->city ?? $project->region ?? $project->location ?? 'Indonesia' }}
                        </p>
                        <div class="mt-4 flex items-center justify-between border-t border-charcoal-100 pt-4 text-sm">
                            <span class="flex items-center gap-2">
                                <x-badge type="{{ $project->status === 'active' ? 'green' : 'navy' }}">{{ ucfirst($project->status) }}</x-badge>
                            </span>
                            <span class="font-semibold text-charcoal-800">IDR {{ number_format($project->contract_value, 0) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full"><x-empty-state title="No projects" /></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Process / CTA --}}
    <section class="relative overflow-hidden bg-charcoal-950 py-20">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 50%, #f95f14 0, transparent 40%), radial-gradient(circle at 80% 20%, #2a4a6f 0, transparent 45%)"></div>
        <div class="container-equip relative">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="reveal">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">How It Works</p>
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-white sm:text-5xl">Renting Equipment, Simplified</h2>
                    <div class="mt-8 space-y-6">
                        @php
                            $steps = [
                                ['01', 'Tell us your project', 'Submit a rental request with your equipment needs, location, and timeline.'],
                                ['02', 'Get a transparent quote', 'Within 24 hours you receive a detailed quotation with fixed daily rates.'],
                                ['03', 'We deliver & operate', 'Fully inspected equipment is delivered on schedule, with certified operators if needed.'],
                            ];
                        @endphp
                        @foreach ($steps as $i => $step)
                            <div class="reveal flex gap-5" style="transition-delay:{{ $i * 0.08 }}s">
                                <span class="font-display text-4xl font-bold text-brand-500/60">{{ $step[0] }}</span>
                                <div>
                                    <h3 class="font-display text-xl font-bold uppercase tracking-wide text-white">{{ $step[1] }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-charcoal-400">{{ $step[2] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="reveal border border-white/10 bg-navy-950/60 p-8 backdrop-blur sm:p-10">
                    <h3 class="font-display text-2xl font-bold uppercase tracking-wide text-white">Ready to get started?</h3>
                    <p class="mt-3 text-sm leading-relaxed text-charcoal-300">
                        Request a quote and our team will respond within one business day with availability and pricing.
                    </p>
                    <a href="{{ route('quote.create') }}" class="btn-brand btn-lg mt-7 w-full">Request a Quote</a>
                    <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-4 text-xs text-charcoal-400">
                        <a href="{{ route('catalog') }}" class="hover:text-white">Browse the fleet →</a>
                        <a href="{{ route('contact') }}" class="hover:text-white">Talk to an expert →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>
