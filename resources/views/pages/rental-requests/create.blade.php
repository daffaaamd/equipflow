<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $base = $isCustomer ? 'customer' : 'admin';
    @endphp

    <x-slot:title>New Rental Request</x-slot:title>
    <x-slot:subtitle>{{ $isCustomer ? 'Submit equipment requirements' : 'Create a request on behalf of a customer' }}</x-slot:subtitle>

    <form method="POST" action="{{ route("$base.rental-requests.store") }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Project Information</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Project Name" name="project_name" :required="true" :error="$errors->first('project_name')" class="sm:col-span-2">
                    <input type="text" name="project_name" class="input" value="{{ old('project_name') }}" required>
                </x-field>
                <x-field label="Project Type" name="project_type" :error="$errors->first('project_type')">
                    <input type="text" name="project_type" class="input" value="{{ old('project_type') }}" placeholder="Construction / Mining…">
                </x-field>
                <x-field label="Project Location" name="project_location" :error="$errors->first('project_location')">
                    <input type="text" name="project_location" class="input" value="{{ old('project_location') }}">
                </x-field>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Equipment Needed</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-field label="Equipment" name="equipment_id" :error="$errors->first('equipment_id')">
                    <select name="equipment_id" class="input">
                        <option value="">Select specific unit (optional)</option>
                        @foreach ($equipment as $eq)
                            <option value="{{ $eq->id }}" @selected(old('equipment_id') == $eq->id)>{{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Or Equipment Category" name="category_id" :error="$errors->first('category_id')">
                    <select name="category_id" class="input">
                        <option value="">Select category (optional)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Quantity" name="quantity" :required="true" :error="$errors->first('quantity')">
                    <input type="number" name="quantity" class="input" value="{{ old('quantity', 1) }}" min="1" max="100" required>
                </x-field>
                <x-field label="Start Date" name="start_date" :required="true" :error="$errors->first('start_date')">
                    <input type="date" name="start_date" class="input" value="{{ old('start_date') }}" required>
                </x-field>
                <x-field label="End Date" name="end_date" :required="true" :error="$errors->first('end_date')">
                    <input type="date" name="end_date" class="input" value="{{ old('end_date') }}" required>
                </x-field>
                <div class="grid gap-3 sm:col-span-2 lg:col-span-1 lg:grid-cols-1 xl:grid-cols-3">
                    @php
                        $extras = [
                            'operator_required' => 'Operator',
                            'transportation_included' => 'Transport',
                            'fuel_included' => 'Fuel',
                        ];
                    @endphp
                    @foreach ($extras as $name => $label)
                        <label class="flex cursor-pointer items-center gap-2 rounded-sm border border-charcoal-200 bg-charcoal-50 px-3 py-2.5 text-xs font-medium text-charcoal-700">
                            <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 accent-brand-500" @checked(old($name))>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <x-field label="Additional Requirements" name="additional_requirements" :error="$errors->first('additional_requirements')" class="sm:col-span-2 lg:col-span-3">
                    <textarea name="additional_requirements" rows="3" class="input">{{ old('additional_requirements') }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route("$base.rental-requests.index") }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Submit Request</button>
        </div>
    </form>
</x-layouts.app>