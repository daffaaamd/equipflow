<x-layouts.app>
    <x-slot:title>Edit Equipment</x-slot:title>
    <x-slot:subtitle>{{ $equipment->equipment_code }} — {{ $equipment->name }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.equipment.update', $equipment->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="grid gap-5">
            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Unit Identity</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                    <x-field label="Equipment Code" name="equipment_code" :required="true" :error="$errors->first('equipment_code')">
                        <input type="text" name="equipment_code" class="input" value="{{ old('equipment_code', $equipment->equipment_code) }}" required>
                    </x-field>
                    <x-field label="Name" name="name" :required="true" :error="$errors->first('name')">
                        <input type="text" name="name" class="input" value="{{ old('name', $equipment->name) }}" required>
                    </x-field>
                    <x-field label="Category" name="category_id" :required="true" :error="$errors->first('category_id')">
                        <select name="category_id" class="input" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $equipment->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Brand" name="brand" :required="true" :error="$errors->first('brand')">
                        <input type="text" name="brand" class="input" value="{{ old('brand', $equipment->brand) }}" required>
                    </x-field>
                    <x-field label="Model" name="model" :required="true" :error="$errors->first('model')">
                        <input type="text" name="model" class="input" value="{{ old('model', $equipment->model) }}" required>
                    </x-field>
                    <x-field label="Year" name="year" :required="true" :error="$errors->first('year')">
                        <input type="number" name="year" class="input" value="{{ old('year', $equipment->year) }}" min="2000" required>
                    </x-field>
                    <x-field label="Serial Number" name="serial_number" :error="$errors->first('serial_number')">
                        <input type="text" name="serial_number" class="input" value="{{ old('serial_number', $equipment->serial_number) }}">
                    </x-field>
                    <x-field label="Condition" name="condition" :required="true" :error="$errors->first('condition')">
                        <select name="condition" class="input" required>
                            @foreach (['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('condition', $equipment->condition) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                        <select name="status" class="input" required>
                            @foreach (['available' => 'Available', 'rented' => 'Rented', 'maintenance' => 'Maintenance', 'unavailable' => 'Unavailable'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $equipment->status) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-field>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Specifications</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        $nums = [
                            'operating_weight' => 'Operating Weight (kg)', 'engine_power' => 'Engine Power (hp)',
                            'bucket_capacity' => 'Bucket Capacity (m³)', 'fuel_capacity' => 'Fuel Capacity (L)',
                            'operating_hours' => 'Operating Hours', 'next_service_hours' => 'Next Service (hours)',
                            'purchase_price' => 'Purchase Price', 'hourly_rate' => 'Hourly Rate (IDR)',
                        ];
                    @endphp
                    @foreach ($nums as $key => $label)
                        <x-field :label="$label" :name="$key" :error="$errors->first($key)">
                            <input type="number" step="0.01" name="{{ $key }}" class="input" value="{{ old($key, $equipment->$key) }}">
                        </x-field>
                    @endforeach
                    <x-field label="Purchase Date" name="purchase_date" :error="$errors->first('purchase_date')">
                        <input type="date" name="purchase_date" class="input" value="{{ old('purchase_date', $equipment->purchase_date ? \Carbon\Carbon::parse($equipment->purchase_date)->toDateString() : '') }}">
                    </x-field>
                    <x-field label="Daily Rate (IDR)" name="daily_rate" :required="true" :error="$errors->first('daily_rate')">
                        <input type="number" step="0.01" name="daily_rate" class="input" value="{{ old('daily_rate', $equipment->daily_rate) }}" required>
                    </x-field>
                    <x-field label="Weekly Rate (IDR)" name="weekly_rate" :error="$errors->first('weekly_rate')">
                        <input type="number" step="0.01" name="weekly_rate" class="input" value="{{ old('weekly_rate', $equipment->weekly_rate) }}">
                    </x-field>
                    <x-field label="Monthly Rate (IDR)" name="monthly_rate" :error="$errors->first('monthly_rate')">
                        <input type="number" step="0.01" name="monthly_rate" class="input" value="{{ old('monthly_rate', $equipment->monthly_rate) }}">
                    </x-field>
                    <x-field label="Deposit (IDR)" name="deposit" :error="$errors->first('deposit')">
                        <input type="number" step="0.01" name="deposit" class="input" value="{{ old('deposit', $equipment->deposit) }}">
                    </x-field>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Location</h3>
                </div>
                <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        $locs = ['current_location' => 'Current Location', 'city' => 'City', 'province' => 'Province', 'region' => 'Region'];
                    @endphp
                    @foreach ($locs as $key => $label)
                        <x-field :label="$label" :name="$key" :error="$errors->first($key)">
                            <input type="text" name="{{ $key }}" class="input" value="{{ old($key, $equipment->$key) }}">
                        </x-field>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-navy-950">
                    <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Details</h3>
                </div>
                <div class="grid gap-4 p-6">
                    <x-field label="Image URL" name="image_url" :error="$errors->first('image_url')">
                        <input type="url" name="image_url" class="input" value="{{ old('image_url', $equipment->image_url) }}" placeholder="https://...">
                    </x-field>
                    <x-field label="Description" name="description" :error="$errors->first('description')">
                        <textarea name="description" rows="4" class="input">{{ old('description', $equipment->description) }}</textarea>
                    </x-field>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.equipment.show', $equipment->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Save Changes</button>
        </div>
    </form>
</x-layouts.app>