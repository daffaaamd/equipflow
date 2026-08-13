<x-layouts.app>
    <x-slot:title>Schedule Maintenance</x-slot:title>
    <x-slot:subtitle>Create a preventive or corrective maintenance record</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.maintenance.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Maintenance Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Equipment" name="equipment_id" :required="true" :error="$errors->first('equipment_id')">
                    <select name="equipment_id" class="input" required>
                        <option value="">Select equipment</option>
                        @foreach ($equipment as $eq)
                            <option value="{{ $eq->id }}" @selected(old('equipment_id') == $eq->id)>{{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Type" name="type" :required="true" :error="$errors->first('type')">
                    <select name="type" class="input" required>
                        <option value="preventive" @selected(old('type') === 'preventive')>Preventive</option>
                        <option value="corrective" @selected(old('type') === 'corrective')>Corrective</option>
                    </select>
                </x-field>
                <x-field label="Title" name="title" :required="true" :error="$errors->first('title')" class="sm:col-span-2">
                    <input type="text" name="title" class="input" value="{{ old('title') }}" placeholder="e.g. 500-hour service" required>
                </x-field>
                <x-field label="Description" name="description" :error="$errors->first('description')" class="sm:col-span-2">
                    <textarea name="description" rows="3" class="input">{{ old('description') }}</textarea>
                </x-field>
                <x-field label="Technician" name="technician" :error="$errors->first('technician')">
                    <input type="text" name="technician" class="input" value="{{ old('technician') }}">
                </x-field>
                <x-field label="Date" name="date" :required="true" :error="$errors->first('date')">
                    <input type="date" name="date" class="input" value="{{ old('date') }}" required>
                </x-field>
                <x-field label="Cost (IDR)" name="cost" :required="true" :error="$errors->first('cost')">
                    <input type="number" step="0.01" min="0" name="cost" class="input" value="{{ old('cost', 0) }}" required>
                </x-field>
                <x-field label="Downtime (hours)" name="downtime_hours" :required="true" :error="$errors->first('downtime_hours')">
                    <input type="number" step="0.1" min="0" name="downtime_hours" class="input" value="{{ old('downtime_hours', 0) }}" required>
                </x-field>
                <x-field label="Parts Used" name="parts_used" :error="$errors->first('parts_used')">
                    <input type="text" name="parts_used" class="input" value="{{ old('parts_used') }}">
                </x-field>
                <x-field label="Next Due Date" name="next_due_date" :error="$errors->first('next_due_date')">
                    <input type="date" name="next_due_date" class="input" value="{{ old('next_due_date') }}">
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        @foreach (['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.maintenance.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Create Maintenance Record</button>
        </div>
    </form>
</x-layouts.app>