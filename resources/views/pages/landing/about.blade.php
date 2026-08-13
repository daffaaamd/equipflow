<x-layouts.landing>
    <section class="bg-charcoal-100 py-16">
        <div class="container-equip">
            {{-- Hero --}}
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="reveal">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">About EquipFlow</p>
                    <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-6xl">Moving Projects Forward Since Day One</h1>
                    <p class="mt-6 text-lg leading-relaxed text-charcoal-600">
                        EquipFlow is a heavy equipment rental and fleet management company serving construction,
                        mining, infrastructure, energy, and plantation sectors. We combine a modern, well-maintained
                        fleet with disciplined operations to keep your project on schedule — and your budget intact.
                    </p>
                    <p class="mt-4 leading-relaxed text-charcoal-600">
                        Every machine in our fleet is inspected, serviced on a strict preventive schedule, and tracked
                        with real-time utilization data. When you rent with EquipFlow, you get more than a machine —
                        you get visibility, reliability, and a partner committed to your uptime.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('catalog') }}" class="btn-brand btn-lg">Explore the Fleet</a>
                        <a href="{{ route('contact') }}" class="btn-outline-navy btn-lg">Get in Touch</a>
                    </div>
                </div>
                <div class="reveal img-reveal is-visible grid grid-cols-2 gap-4">
                    <img src="/img/categories/hydraulic-excavator.jpg" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="Excavator working" class="aspect-[3/4] w-full object-cover">
                    <img src="/img/categories/crawler-crane.jpg" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="Heavy machinery fleet" class="mt-8 aspect-[3/4] w-full object-cover">
                </div>
            </div>

            {{-- Stats band --}}
            <div class="reveal mt-16 grid grid-cols-2 gap-px overflow-hidden border border-charcoal-200 bg-charcoal-200 lg:grid-cols-4">
                @php
                    $statsList = [
                        [$stats['equipment'], 'Fleet Units', 'text-brand-500'],
                        [$stats['operators'], 'Certified Operators', 'text-navy-900'],
                        [$stats['customers'], 'Active Customers', 'text-navy-900'],
                        [$stats['projects'], 'Projects Delivered', 'text-navy-900'],
                    ];
                @endphp
                @foreach ($statsList as $s)
                    <div class="bg-white px-6 py-8 text-center">
                        <p class="font-display text-4xl font-bold {{ $s[2] }}" data-counter="{{ $s[0] }}">{{ $s[0] }}</p>
                        <p class="mt-2 text-xs font-semibold uppercase tracking-widest text-charcoal-500">{{ $s[1] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Values --}}
            <div class="mt-16 grid gap-6 md:grid-cols-3">
                @php
                    $values = [
                        ['01', 'Reliability First', 'Preventive maintenance schedules and rigorous inspections keep unplanned downtime at a minimum.'],
                        ['02', 'Transparent Pricing', 'Fixed daily, weekly, and monthly rates published openly. No hidden charges, ever.'],
                        ['03', 'Operational Discipline', 'Certified operators, documented delivery, and real-time utilization reporting on every contract.'],
                    ];
                @endphp
                @foreach ($values as $i => $v)
                    <div class="reveal border-t-4 border-t-brand-500 bg-white p-7" style="transition-delay:{{ $i * 0.08 }}s">
                        <span class="font-display text-4xl font-bold text-charcoal-200">{{ $v[0] }}</span>
                        <h3 class="mt-3 font-display text-2xl font-bold uppercase tracking-wide text-navy-900">{{ $v[1] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-charcoal-500">{{ $v[2] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- CTA --}}
            <div class="reveal mt-16 flex flex-col items-center justify-between gap-6 bg-navy-950 px-8 py-10 sm:flex-row sm:px-12">
                <div>
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-white">Partner with EquipFlow</h2>
                    <p class="mt-2 text-charcoal-300">Get a tailored quotation for your upcoming project.</p>
                </div>
                <a href="{{ route('quote.create') }}" class="btn-brand btn-lg shrink-0">Request a Quote</a>
            </div>
        </div>
    </section>
</x-layouts.landing>
