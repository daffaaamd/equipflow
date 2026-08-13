<x-layouts.landing>
    <section class="bg-charcoal-100 py-14">
        <div class="container-equip">
            <div class="mx-auto max-w-4xl">
                <div class="reveal text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-500">Request a Quote</p>
                    <h1 class="mt-3 font-display text-4xl font-bold uppercase tracking-tight text-navy-900 sm:text-5xl">Tell Us What You Need</h1>
                    <p class="mx-auto mt-4 max-w-2xl text-charcoal-600">
                        Complete the form below and our rental specialists will respond within one business day with
                        availability and a transparent quotation.
                    </p>
                </div>

                @if ($customer)
                    <div class="reveal mt-6 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        Requesting as <strong>{{ $customer->company_name }}</strong> — we will pre-fill your details automatically.
                    </div>
                @endif

                <form method="POST" action="{{ route('quote.store') }}" class="reveal mt-8">
                    @csrf

                    <div class="card overflow-hidden">
                        <div class="card-header bg-navy-950">
                            <h2 class="font-display text-xl font-bold uppercase tracking-wide text-white">Project Details</h2>
                            <p class="text-sm text-charcoal-400">Fields marked * are required</p>
                        </div>

                        <div class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                            <x-field label="Company Name" name="company_name" :required="true" class="sm:col-span-2" :error="$errors->first('company_name')">
                                <input type="text" name="company_name" class="input" value="{{ old('company_name', $customer?->company_name) }}" required>
                            </x-field>

                            <x-field label="Contact Person" name="contact_person" :required="true" :error="$errors->first('contact_person')">
                                <input type="text" name="contact_person" class="input" value="{{ old('contact_person', $customer?->contact_person) }}" required>
                            </x-field>
                            <x-field label="Phone" name="phone" :error="$errors->first('phone')">
                                <input type="text" name="phone" class="input" value="{{ old('phone', $customer?->phone) }}" placeholder="+62 ...">
                            </x-field>

                            <x-field label="Email" name="email" :required="true" class="sm:col-span-2" :error="$errors->first('email')">
                                <input type="email" name="email" class="input" value="{{ old('email', $customer?->email) }}" required>
                            </x-field>

                            <x-field label="Project Name" name="project_name" :required="true" class="sm:col-span-2" :error="$errors->first('project_name')">
                                <input type="text" name="project_name" class="input" value="{{ old('project_name') }}" placeholder="e.g. Apartemen Sudirman Tower 3" required>
                            </x-field>

                            <x-field label="Project Type" name="project_type" :error="$errors->first('project_type')">
                                <input type="text" name="project_type" class="input" value="{{ old('project_type') }}" placeholder="Construction / Mining / etc.">
                            </x-field>
                            <x-field label="Project Location" name="project_location" :error="$errors->first('project_location')">
                                <input type="text" name="project_location" class="input" value="{{ old('project_location') }}" placeholder="City, Province">
                            </x-field>

                            <x-field label="Equipment Category" name="category_id" :error="$errors->first('category_id')">
                                <select name="category_id" id="category-select" class="input">
                                    <option value="">Any category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </x-field>

                            <x-field label="Specific Equipment (optional)" name="equipment_id" :error="$errors->first('equipment_id')">
                                <select name="equipment_id" class="input">
                                    <option value="">No preference — recommend equipment</option>
                                    @foreach ($equipment as $eq)
                                        <option value="{{ $eq->id }}" @selected((string) old('equipment_id', $selectedEquipment) === (string) $eq->id)>
                                            {{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-field>

                            <x-field label="Quantity" name="quantity" :required="true" :error="$errors->first('quantity')">
                                <input type="number" name="quantity" class="input" value="{{ old('quantity', 1) }}" min="1" max="100" required>
                            </x-field>
                            <x-field label="Rental Period" name="dates" :error="$errors->first('start_date') . $errors->first('end_date')">
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="date" name="start_date" class="input" value="{{ old('start_date') }}" required>
                                    <input type="date" name="end_date" class="input" value="{{ old('end_date') }}" required>
                                </div>
                            </x-field>

                            <div class="grid gap-3 sm:col-span-2 sm:grid-cols-3">
                                @php
                                    $extras = [
                                        'operator_required' => 'Operator Required',
                                        'transportation_included' => 'Include Transportation',
                                        'fuel_included' => 'Fuel Included',
                                    ];
                                @endphp
                                @foreach ($extras as $name => $label)
                                    <label class="flex cursor-pointer items-center gap-3 border border-charcoal-200 bg-charcoal-50 px-4 py-3 text-sm font-medium text-charcoal-700">
                                        <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 accent-brand-500" @checked(old($name))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                            <x-field label="Additional Requirements" name="additional_requirements" class="sm:col-span-2" :error="$errors->first('additional_requirements')">
                                <textarea name="additional_requirements" rows="4" class="input" placeholder="Site access, working hours, fuel arrangements, etc.">{{ old('additional_requirements') }}</textarea>
                            </x-field>

                            @if ($errors->any())
                                <div class="sm:col-span-2">
                                    <x-alert type="error" />
                                </div>
                            @endif

                            <div class="sm:col-span-2 flex flex-col items-center gap-4 border-t border-charcoal-200 pt-6">
                                <button type="submit" class="btn-brand btn-lg w-full sm:w-auto sm:px-12">Submit Quote Request</button>
                                <p class="text-xs text-charcoal-400">No payment required — this is a no-obligation request.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.landing>
