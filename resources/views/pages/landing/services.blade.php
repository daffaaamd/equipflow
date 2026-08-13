<x-layouts.landing>
    @php
        $services = [
            ['Rental & Leasing', 'Flexible daily, weekly, monthly, and long-term leasing on excavators, dozers, cranes, loaders, haul trucks, and more.', 'flex h-12 w-12 items-center justify-center bg-brand-500/15 text-brand-500'],
            ['Certified Operators', 'Fully trained and certified operators assigned to your project, with strict safety and reporting standards.', 'flex h-12 w-12 items-center justify-center bg-navy-900/10 text-navy-900'],
            ['Logistics & Delivery', 'End-to-end transport with lowbed trailers, route planning, permits, and on-site positioning.', 'flex h-12 w-12 items-center justify-center bg-brand-500/15 text-brand-500'],
            ['Preventive Maintenance', 'Scheduled servicing and telematics monitoring to maximize uptime across your rented fleet.', 'flex h-12 w-12 items-center justify-center bg-navy-900/10 text-navy-900'],
            ['Fleet Monitoring', 'Real-time utilization and operating-hour reporting so you always know how your machines are performing.', 'flex h-12 w-12 items-center justify-center bg-brand-500/15 text-brand-500'],
            ['Repair Support', 'On-site and workshop repair support with rapid spare-part sourcing to minimize downtime.', 'flex h-12 w-12 items-center justify-center bg-navy-900/10 text-navy-900'],
        ];
        $icons = [
            '<path d="M7.5 3.5l9 2.25v12.5l-9 2.25V3.5z"/><path d="M7.5 7.5h9M7.5 12.5h9M7.5 17h9M16.5 6v12M7.5 6v12"/>',
            '<circle cx="12" cy="8" r="4"/><path d="M5 21c.6-3.6 3.6-6 7-6s6.4 2.4 7 6"/>',
            '<path d="M1 4h13v12H1z"/><path d="M14 8h4l3 4v4h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',
            '<path d="M14.7 6.3a4.5 4.5 0 00-6.1 6.1L3 18l3 3 5.6-5.6a4.5 4.5 0 006.1-6.1L14 13l-3-3 3.7-3.7z"/>',
            '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
            '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6l8-3z"/>',
        ];
    @endphp

    <section class="bg-charcoal-100 py-16">
        <div class="container-equip">
            <div class="reveal max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Services</p>
                <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-6xl">Complete Rental Support</h1>
                <p class="mt-5 text-lg text-charcoal-600">
                    Equipment is only half the equation. We back every rental with logistics, operations,
                    and maintenance support so your project never stops.
                </p>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $i => $svc)
                    <div class="reveal card group p-7 transition-shadow hover:shadow-md" style="transition-delay:{{ $i * 0.06 }}s">
                        <div class="{{ $svc[2] }}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">{!! $icons[$i] !!}</svg>
                        </div>
                        <h3 class="mt-5 font-display text-2xl font-bold uppercase tracking-wide text-navy-900">{{ $svc[0] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-charcoal-500">{{ $svc[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="reveal mt-16 grid gap-px overflow-hidden border border-charcoal-200 bg-charcoal-200 sm:grid-cols-3">
                @php
                    $commitments = [
                        ['24/7', 'Support line for urgent operational issues'],
                        ['24h', 'Quotation turnaround on request'],
                        ['100%', 'Units inspected before every deployment'],
                    ];
                @endphp
                @foreach ($commitments as $c)
                    <div class="bg-white px-6 py-8 text-center">
                        <p class="font-display text-4xl font-bold text-brand-600">{{ $c[0] }}</p>
                        <p class="mt-2 text-sm text-charcoal-500">{{ $c[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="reveal mt-16 flex flex-col items-center justify-between gap-6 bg-navy-950 px-8 py-10 sm:flex-row">
                <div>
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-white">Ready to move forward?</h2>
                    <p class="mt-2 text-charcoal-300">Speak with our rental team today.</p>
                </div>
                <a href="{{ route('quote.create') }}" class="btn-brand btn-lg shrink-0">Request a Quote</a>
            </div>
        </div>
    </section>
</x-layouts.landing>