<x-layouts.landing>
    @php
        $statusBadges = ['available' => 'green', 'rented' => 'amber', 'maintenance' => 'red', 'unavailable' => 'gray'];
    @endphp

    {{-- Page header --}}
    <section class="relative overflow-hidden bg-navy-950 py-16">
        <div class="absolute inset-0 opacity-20" style="background-image:url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5');background-size:cover;background-position:center"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/85 to-navy-950/50"></div>
        <div class="container-equip relative">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-400">Equipment Catalog</p>
            <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-white sm:text-5xl">Explore the Fleet</h1>
            <p class="mt-3 max-w-xl text-charcoal-300">Every unit is inspected, maintained, and ready for deployment. Filter by type, brand, and location.</p>
        </div>
    </section>

    <section class="bg-charcoal-100 py-10">
        <div class="container-equip">
            <div class="grid gap-8 lg:grid-cols-[280px_1fr]">
                {{-- Filters --}}
                <form method="GET" action="{{ route('catalog') }}" class="reveal h-fit lg:sticky lg:top-20">
                    <div class="card p-5">
                        <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">Filter</h3>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="label">Search</label>
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input" placeholder="Excavator, brand, model…">
                            </div>
                            <div>
                                <label class="label">Category</label>
                                <select name="category" class="input" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(($filters['category'] ?? '') == $cat->id)>{{ $cat->name }} ({{ $cat->equipment_count }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Brand</label>
                                <select name="brand" class="input" onchange="this.form.submit()">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand }}" @selected(($filters['brand'] ?? '') === $brand)>{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Location</label>
                                <select name="location" class="input" onchange="this.form.submit()">
                                    <option value="">All Locations</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc }}" @selected(($filters['location'] ?? '') === $loc)>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Minimum Weight (tons)</label>
                                <select name="capacity" class="input" onchange="this.form.submit()">
                                    <option value="">Any</option>
                                    @foreach ([10, 20, 30, 40, 50, 100] as $t)
                                        <option value="{{ $t * 1000 }}" @selected(($filters['capacity'] ?? '') == $t * 1000)>{{ $t }} t</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Availability</label>
                                <select name="status" class="input" onchange="this.form.submit()">
                                    <option value="">Any Status</option>
                                    @foreach (['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance'] as $val => $label)
                                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Sort By</label>
                                <select name="sort" class="input" onchange="this.form.submit()">
                                    @php
                                        $sorts = ['' => 'Code', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'newest' => 'Newest', 'hours_asc' => 'Lowest Hours'];
                                    @endphp
                                    @foreach ($sorts as $val => $label)
                                        <option value="{{ $val }}" @selected(($filters['sort'] ?? '') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-navy btn-md w-full">Apply Filters</button>
                            @if (count(array_filter($filters)))
                                <a href="{{ route('catalog') }}" class="btn-outline btn-md w-full">Clear Filters</a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Results --}}
                <div>
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-charcoal-600">
                            <span class="font-semibold text-navy-900">{{ $equipment->total() }}</span> unit{{ $equipment->total() == 1 ? '' : 's' }} found
                        </p>
                        <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-charcoal-500">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Live fleet availability
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse ($equipment as $eq)
                            <article class="card group flex flex-col overflow-hidden">
                                <div class="img-reveal relative aspect-[4/3] overflow-hidden bg-navy-900">
                                    <img src="{{ $eq->primaryImage()?->url ?? '/img/placeholder.svg' }}"
                                         onerror="this.onerror=null;this.src='/img/placeholder.svg'"
                                         alt="{{ $eq->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <div class="absolute left-3 top-3"><x-badge type="{{ $statusBadges[$eq->status] ?? 'gray' }}">{{ $eq->status_label }}</x-badge></div>
                                    <div class="absolute right-3 top-3"><x-badge type="outline" class="!bg-white/90 backdrop-blur">{{ $eq->category->name }}</x-badge></div>
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <p class="text-[11px] font-semibold uppercase tracking-widest text-charcoal-400">{{ $eq->equipment_code }} · {{ $eq->year }}</p>
                                    <h3 class="mt-1 font-display text-lg font-bold uppercase leading-tight text-navy-900">{{ $eq->brand }} {{ $eq->model }}</h3>
                                    <p class="mt-1 line-clamp-2 text-sm text-charcoal-500">{{ $eq->name }}</p>
                                    <div class="mt-3 flex items-center gap-3 text-xs text-charcoal-500">
                                        <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>{{ $eq->current_location ?? '-' }}</span>
                                        <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>{{ number_format($eq->operating_hours, 0) }} hrs</span>
                                    </div>
                                    <div class="mt-4 flex items-end justify-between border-t border-charcoal-200 pt-4">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-widest text-charcoal-400">Daily Rate</p>
                                            <p class="font-display text-xl font-bold text-brand-600">IDR {{ number_format($eq->daily_rate) }}</p>
                                        </div>
                                        <a href="{{ route('catalog.show', $eq->id) }}" class="btn-outline-navy btn-sm">Details</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full">
                                <x-empty-state icon="search" title="No equipment matches your filters"
                                               description="Try adjusting your search criteria or clearing filters.">
                                    <x-slot:action>
                                        <a href="{{ route('catalog') }}" class="btn-brand btn-md">Clear Filters</a>
                                    </x-slot:action>
                                </x-empty-state>
                            </div>
                        @endforelse
                    </div>

                    <x-pagination :links="$equipment" />
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>
