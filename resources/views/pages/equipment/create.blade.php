<x-layouts.app>
    <x-slot:title>Add Equipment</x-slot:title>
    <x-slot:subtitle>Register a new unit to the fleet</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.equipment.store') }}" class="space-y-5">
        @csrf
        <div class="grid gap-5">
            {{-- Identity --}}
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Unit Identity</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                    <x-field label="Equipment Code" name="equipment_code" :required="true" :error="$errors->first('equipment_code')">
                        <input type="text" name="equipment_code" class="input" value="{{ old('equipment_code') }}" placeholder="EXC-001" required>
                    </x-field>
                    <x-field label="Name" name="name" :required="true" :error="$errors->first('name')">
                        <input type="text" name="name" class="input" value="{{ old('name') }}" placeholder="Hydraulic Excavator 20t" required>
                    </x-field>
                    <x-field label="Category" name="category_id" :required="true" :error="$errors->first('category_id')">
                        <select name="category_id" class="input" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Brand" name="brand" :required="true" :error="$errors->first('brand')">
                        <input type="text" name="brand" class="input" value="{{ old('brand') }}" placeholder="Komatsu" required>
                    </x-field>
                    <x-field label="Model" name="model" :required="true" :error="$errors->first('model')">
                        <input type="text" name="model" class="input" value="{{ old('model') }}" placeholder="PC200-8" required>
                    </x-field>
                    <x-field label="Year" name="year" :required="true" :error="$errors->first('year')">
                        <input type="number" name="year" class="input" value="{{ old('year', date('Y')) }}" min="2000" required>
                    </x-field>
                    <x-field label="Serial Number" name="serial_number" :error="$errors->first('serial_number')">
                        <input type="text" name="serial_number" class="input" value="{{ old('serial_number') }}">
                    </x-field>
                    <x-field label="Condition" name="condition" :required="true" :error="$errors->first('condition')">
                        <select name="condition" class="input" required>
                            @foreach (['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('condition') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                        <select name="status" class="input" required>
                            @foreach (['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance', 'unavailable' => 'Unavailable'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Specifications</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    <x-field label="Operating Weight (kg)" name="operating_weight" :error="$errors->first('operating_weight')">
                        <input type="number" step="0.01" name="operating_weight" class="input" value="{{ old('operating_weight') }}">
                    </x-field>
                    <x-field label="Engine Power (hp)" name="engine_power" :error="$errors->first('engine_power')">
                        <input type="number" step="0.01" name="engine_power" class="input" value="{{ old('engine_power') }}">
                    </x-field>
                    <x-field label="Bucket Capacity (m³)" name="bucket_capacity" :error="$errors->first('bucket_capacity')">
                        <input type="number" step="0.01" name="bucket_capacity" class="input" value="{{ old('bucket_capacity') }}">
                    </x-field>
                    <x-field label="Fuel Capacity (L)" name="fuel_capacity" :error="$errors->first('fuel_capacity')">
                        <input type="number" step="0.01" name="fuel_capacity" class="input" value="{{ old('fuel_capacity') }}">
                    </x-field>
                    <x-field label="Operating Hours" name="operating_hours" :error="$errors->first('operating_hours')">
                        <input type="number" step="0.01" name="operating_hours" class="input" value="{{ old('operating_hours', 0) }}">
                    </x-field>
                    <x-field label="Next Service (hours)" name="next_service_hours" :error="$errors->first('next_service_hours')">
                        <input type="number" step="0.01" name="next_service_hours" class="input" value="{{ old('next_service_hours') }}">
                    </x-field>
                    <x-field label="Purchase Price" name="purchase_price" :error="$errors->first('purchase_price')">
                        <input type="number" step="0.01" name="purchase_price" class="input" value="{{ old('purchase_price') }}">
                    </x-field>
                    <x-field label="Purchase Date" name="purchase_date" :error="$errors->first('purchase_date')">
                        <input type="date" name="purchase_date" class="input" value="{{ old('purchase_date') }}">
                    </x-field>
                </div>
            </div>

            {{-- Location --}}
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Location</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    <x-field label="Current Location" name="current_location" :error="$errors->first('current_location')">
                        <input type="text" name="current_location" class="input" value="{{ old('current_location') }}">
                    </x-field>
                    <x-field label="City" name="city" :error="$errors->first('city')">
                        <input type="text" name="city" class="input" value="{{ old('city') }}">
                    </x-field>
                    <x-field label="Province" name="province" :error="$errors->first('province')">
                        <input type="text" name="province" class="input" value="{{ old('province') }}">
                    </x-field>
                    <x-field label="Region" name="region" :error="$errors->first('region')">
                        <input type="text" name="region" class="input" value="{{ old('region') }}">
                    </x-field>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Pricing</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    <x-field label="Daily Rate (IDR)" name="daily_rate" :required="true" :error="$errors->first('daily_rate')">
                        <input type="number" step="0.01" name="daily_rate" class="input" value="{{ old('daily_rate') }}" required>
                    </x-field>
                    <x-field label="Weekly Rate (IDR)" name="weekly_rate" :error="$errors->first('weekly_rate')">
                        <input type="number" step="0.01" name="weekly_rate" class="input" value="{{ old('weekly_rate') }}">
                    </x-field>
                    <x-field label="Monthly Rate (IDR)" name="monthly_rate" :error="$errors->first('monthly_rate')">
                        <input type="number" step="0.01" name="monthly_rate" class="input" value="{{ old('monthly_rate') }}">
                    </x-field>
                    <x-field label="Hourly Rate (IDR)" name="hourly_rate" :error="$errors->first('hourly_rate')">
                        <input type="number" step="0.01" name="hourly_rate" class="input" value="{{ old('hourly_rate') }}">
                    </x-field>
                    <x-field label="Deposit (IDR)" name="deposit" :error="$errors->first('deposit')">
                        <input type="number" step="0.01" name="deposit" class="input" value="{{ old('deposit') }}">
                    </x-field>
                </div>
            </div>

            {{-- Description & image --}}
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Details</h3>
                </div>
                <div class="grid gap-4 p-6">
                    <x-field label="Image URL" name="image_url" :error="$errors->first('image_url')">
                        <input type="url" name="image_url" class="input" value="{{ old('image_url') }}" placeholder="https://...">
                    </x-field>
                    <x-field label="Description" name="description" :error="$errors->first('description')">
                        <textarea name="description" rows="4" class="input" placeholder="Unit features, capabilities, attachments…">{{ old('description') }}</textarea>
                    </x-field>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.equipment.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Create Equipment</button>
        </div>
    </form>
</x-layouts.app>