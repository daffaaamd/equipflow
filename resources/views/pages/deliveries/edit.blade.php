<x-layouts.app>
    <x-slot:title>Edit Delivery</x-slot:title>
    <x-slot:subtitle>{{ $delivery->delivery_number }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.deliveries.update', $delivery->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Delivery Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Contract" name="contract_id" :error="$errors->first('contract_id')">
                    <select name="contract_id" class="input">
                        <option value="">Select contract</option>
                        @foreach ($contracts as $c)
                            <option value="{{ $c->id }}" @selected(old('contract_id', $delivery->contract_id) == $c->id)>{{ $c->contract_number }} — {{ $c->customer?->company_name }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Equipment" name="equipment_id" :error="$errors->first('equipment_id')">
                    <select name="equipment_id" class="input">
                        <option value="">Select equipment</option>
                        @foreach ($equipment as $eq)
                            <option value="{{ $eq->id }}" @selected(old('equipment_id', $delivery->equipment_id) == $eq->id)>{{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Pickup Location" name="pickup_location" :error="$errors->first('pickup_location')">
                    <input type="text" name="pickup_location" class="input" value="{{ old('pickup_location', $delivery->pickup_location) }}">
                </x-field>
                <x-field label="Destination" name="destination" :error="$errors->first('destination')">
                    <input type="text" name="destination" class="input" value="{{ old('destination', $delivery->destination) }}">
                </x-field>
                <x-field label="Delivery Date" name="delivery_date" :error="$errors->first('delivery_date')">
                    <input type="date" name="delivery_date" class="input" value="{{ old('delivery_date', $delivery->delivery_date?->toDateString()) }}">
                </x-field>
                <x-field label="Estimated Arrival" name="estimated_arrival" :error="$errors->first('estimated_arrival')">
                    <input type="date" name="estimated_arrival" class="input" value="{{ old('estimated_arrival', $delivery->estimated_arrival?->toDateString()) }}">
                </x-field>
                <x-field label="Driver Name" name="driver_name" :error="$errors->first('driver_name')">
                    <input type="text" name="driver_name" class="input" value="{{ old('driver_name', $delivery->driver_name) }}">
                </x-field>
                <x-field label="Driver Phone" name="driver_phone" :error="$errors->first('driver_phone')">
                    <input type="text" name="driver_phone" class="input" value="{{ old('driver_phone', $delivery->driver_phone) }}">
                </x-field>
                <x-field label="Transport Vehicle" name="transport_vehicle" :error="$errors->first('transport_vehicle')">
                    <input type="text" name="transport_vehicle" class="input" value="{{ old('transport_vehicle', $delivery->transport_vehicle) }}">
                </x-field>
                <x-field label="Plate Number" name="plate_number" :error="$errors->first('plate_number')">
                    <input type="text" name="plate_number" class="input" value="{{ old('plate_number', $delivery->plate_number) }}">
                </x-field>
                <x-field label="Status" name="status" :error="$errors->first('status')">
                    <select name="status" class="input">
                        @foreach (['scheduled' => 'Scheduled', 'preparing' => 'Preparing', 'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'confirmed' => 'Confirmed'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $delivery->status) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes', $delivery->notes) }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Delivery</button>
        </div>
    </form>
</x-layouts.app>