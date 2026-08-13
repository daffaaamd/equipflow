<x-layouts.landing>
    <section class="bg-charcoal-100 py-16">
        <div class="container-equip">
            <div class="reveal max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Contact</p>
                <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-6xl">Talk to Our Team</h1>
                <p class="mt-5 text-lg text-charcoal-600">
                    Whether you need one machine for a week or a full fleet for a year, we are ready to help.
                </p>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-3">
                {{-- Contact info --}}
                <div class="space-y-5">
                    @php
                        $contacts = [
                            ['Head Office', 'Menara EquipFlow Lt. 18, Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan 12190, Indonesia', 'M'],
                            ['Phone', '+62 21 5050 1800 (Mon–Fri, 08:00–17:00 WIB)', 'P'],
                            ['Email', 'info@equipflow.id · quotes@equipflow.id', 'E'],
                            ['Sales Hotline', '+62 811 1800 500 (24/7)', 'H'],
                        ];
                    @endphp
                    @foreach ($contacts as $c)
                        <div class="card flex gap-4 p-5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center bg-brand-500 font-display text-lg font-bold text-white">{{ $c[2] }}</span>
                            <div>
                                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-navy-900">{{ $c[0] }}</h3>
                                <p class="mt-1 text-sm text-charcoal-600">{{ $c[1] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Form --}}
                <div class="card p-7 lg:col-span-2">
                    <h2 class="font-display text-2xl font-bold uppercase tracking-wide text-navy-900">Send a Message</h2>
                    <p class="mt-1 text-sm text-charcoal-500">We usually respond within one business day.</p>

                    <form method="POST" action="{{ route('quote.store') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                        @csrf
                        <x-field label="Company Name" name="company_name" :required="true">
                            <input type="text" name="company_name" class="input" value="{{ old('company_name') }}" required>
                        </x-field>
                        <x-field label="Contact Person" name="contact_person" :required="true">
                            <input type="text" name="contact_person" class="input" value="{{ old('contact_person') }}" required>
                        </x-field>
                        <x-field label="Email" name="email" :required="true">
                            <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                        </x-field>
                        <x-field label="Phone" name="phone">
                            <input type="text" name="phone" class="input" value="{{ old('phone') }}">
                        </x-field>
                        <x-field label="Project Name" name="project_name" :required="true" class="sm:col-span-2">
                            <input type="text" name="project_name" class="input" value="{{ old('project_name') }}" placeholder="Project or requirement" required>
                        </x-field>
                        <x-field label="Message" name="additional_requirements" class="sm:col-span-2">
                            <textarea name="additional_requirements" rows="5" class="input" placeholder="Tell us about your equipment needs, timeline, and location.">{{ old('additional_requirements') }}</textarea>
                        </x-field>
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="start_date" value="{{ now()->toDateString() }}">
                        <input type="hidden" name="end_date" value="{{ now()->addWeek()->toDateString() }}">
                        <div class="sm:col-span-2">
                            <button type="submit" class="btn-brand btn-lg w-full sm:w-auto sm:px-12">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>