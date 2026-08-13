<x-layouts.landing>
    @php
        $industries = [
            ['Construction', 'Earthmoving, site preparation, foundation work, and material handling for residential and commercial projects.', '/img/categories/hydraulic-excavator.jpg', ['Excavators', 'Wheel Loaders', 'Compactors']],
            ['Mining', 'High-capacity excavation, hauling, and crushing support built for continuous operations.', '/img/categories/articulated-dump-truck.jpg', ['HD Dozers', 'Haul Trucks', 'Excavators']],
            ['Infrastructure', 'Road, bridge, and utility equipment for long-duration public works programs.', '/img/categories/motor-grader.jpg', ['Motor Graders', 'Cranes', 'Rollers']],
            ['Plantation', 'Land clearing, hauling, and material handling solutions for estate operations.', '/img/categories/bulldozer.jpg', ['Bulldozers', 'Excavators', 'Forklifts']],
            ['Energy', 'Rigging, lifting, and heavy transport equipment for power and energy projects.', '/img/categories/mobile-crane.jpg', ['Mobile Cranes', 'Telescopic Handlers', 'Trailers']],
            ['Industrial', 'Material handling and logistics equipment for factory and warehouse operations.', '/img/categories/forklift.jpg', ['Forklifts', 'Reach Stackers', 'Telehandlers']],
        ];
    @endphp

    <section class="bg-charcoal-100 py-16">
        <div class="container-equip">
            <div class="reveal max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Solutions</p>
                <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-6xl">Industry-Focused Fleet Solutions</h1>
                <p class="mt-5 text-lg text-charcoal-600">
                    Every sector has different demands — machine types, duty cycles, and uptime requirements.
                    We structure each fleet solution around your operation.
                </p>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-2">
                @foreach ($industries as $i => $ind)
                    <article class="reveal card overflow-hidden">
                        <div class="img-reveal relative aspect-[16/9] overflow-hidden bg-navy-900">
                            <img src="{{ $ind[2] }}" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="{{ $ind[0] }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-transparent to-transparent"></div>
                            <h2 class="absolute bottom-4 left-5 font-display text-3xl font-bold uppercase tracking-wide text-white">{{ $ind[0] }}</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-sm leading-relaxed text-charcoal-600">{{ $ind[1] }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($ind[3] as $eq)
                                    <x-badge type="navy">{{ $eq }}</x-badge>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="reveal mt-16 flex flex-col items-center justify-between gap-6 bg-navy-950 px-8 py-10 text-center sm:flex-row sm:text-left">
                <div>
                    <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-white">Need a custom solution?</h2>
                    <p class="mt-2 text-charcoal-300">Tell us about your project and we will recommend the right fleet.</p>
                </div>
                <a href="{{ route('quote.create') }}" class="btn-brand btn-lg shrink-0">Start a Conversation</a>
            </div>
        </div>
    </section>
</x-layouts.landing>