<x-layouts.app>
    <x-slot:title>Edit Operator</x-slot:title>
    <x-slot:subtitle>{{ $operator->name }} ({{ $operator->operator_code }})</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.operators.update', $operator->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Personal Information</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Full Name" name="name" :required="true" :error="$errors->first('name')">
                    <input type="text" name="name" class="input" value="{{ old('name', $operator->name) }}" required>
                </x-field>
                <x-field label="Email" name="email" :error="$errors->first('email')">
                    <input type="email" name="email" class="input" value="{{ old('email', $operator->email) }}">
                </x-field>
                <x-field label="Phone" name="phone" :error="$errors->first('phone')">
                    <input type="text" name="phone" class="input" value="{{ old('phone', $operator->phone) }}">
                </x-field>
                <x-field label="License Number" name="license_number" :error="$errors->first('license_number')">
                    <input type="text" name="license_number" class="input" value="{{ old('license_number', $operator->license_number) }}">
                </x-field>
                <x-field label="Years of Experience" name="years_experience" :error="$errors->first('years_experience')">
                    <input type="number" min="0" name="years_experience" class="input" value="{{ old('years_experience', $operator->years_experience) }}">
                </x-field>
                <x-field label="Working Hours" name="working_hours" :error="$errors->first('working_hours')">
                    <input type="number" step="0.1" min="0" name="working_hours" class="input" value="{{ old('working_hours', $operator->working_hours) }}">
                </x-field>
                <x-field label="Certification" name="certification" :error="$errors->first('certification')">
                    <input type="text" name="certification" class="input" value="{{ old('certification', $operator->certification) }}">
                </x-field>
                <x-field label="Certification Expiry" name="certification_expiry" :error="$errors->first('certification_expiry')">
                    <input type="date" name="certification_expiry" class="input" value="{{ old('certification_expiry', $operator->certification_expiry?->toDateString()) }}">
                </x-field>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Assignment</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Assigned Equipment" name="assigned_equipment_id" :error="$errors->first('assigned_equipment_id')">
                    <select name="assigned_equipment_id" class="input">
                        <option value="">Not assigned</option>
                        @foreach ($equipment as $eq)
                            <option value="{{ $eq->id }}" @selected(old('assigned_equipment_id', $operator->assigned_equipment_id) == $eq->id)>{{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Project" name="project_id" :error="$errors->first('project_id')">
                    <select name="project_id" class="input">
                        <option value="">No project</option>
                        @foreach ($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('project_id', $operator->project_id) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Availability" name="availability" :required="true" :error="$errors->first('availability')">
                    <select name="availability" class="input" required>
                        @foreach (['available' => 'Available', 'assigned' => 'Assigned', 'on_leave' => 'On Leave'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('availability', $operator->availability) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $operator->status) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes', $operator->notes) }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.operators.show', $operator->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Operator</button>
        </div>
    </form>
</x-layouts.app>