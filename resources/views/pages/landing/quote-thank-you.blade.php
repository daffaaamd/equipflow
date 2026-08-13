<x-layouts.landing>
    <section class="flex min-h-[70vh] items-center bg-charcoal-100 py-20">
        <div class="container-equip">
            <div class="mx-auto max-w-2xl">
                <div class="reveal text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <h1 class="mt-6 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-5xl">Request Received</h1>
                    <p class="mt-4 text-lg text-charcoal-600">
                        @if ($requestNumber)
                            Your request <strong class="text-navy-900">{{ $requestNumber }}</strong> has been submitted successfully.
                        @else
                            Your request has been submitted successfully.
                        @endif
                    </p>
                    <p class="mt-3 text-charcoal-500">
                        Our rental team will review your requirements and send you a quotation within one business day.
                    </p>
                </div>

                <div class="reveal mt-10 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('catalog') }}" class="btn-outline-navy btn-lg">Browse More Equipment</a>
                    <a href="{{ route('landing') }}" class="btn-brand btn-lg">Back to Home</a>
                </div>

                <div class="reveal mt-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @php
                        $next = [
                            ['Review', 'Our specialists check availability for your dates.'],
                            ['Quotation', 'You receive a fixed, transparent quotation.'],
                            ['Delivery', 'Equipment is delivered on schedule.'],
                        ];
                    @endphp
                    @foreach ($next as $i => $n)
                        <div class="border border-charcoal-200 bg-white p-5">
                            <span class="font-display text-2xl font-bold text-brand-500">0{{ $i + 1 }}</span>
                            <h3 class="mt-2 font-display text-lg font-bold uppercase text-navy-900">{{ $n[0] }}</h3>
                            <p class="mt-1 text-sm text-charcoal-500">{{ $n[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>
