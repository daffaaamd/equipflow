<x-layouts.app>
    <x-slot:title>Edit Project</x-slot:title>
    <x-slot:subtitle>{{ $project->name }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.projects.update', $project->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Project Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Project Name" name="name" :required="true" :error="$errors->first('name')" class="sm:col-span-2">
                    <input type="text" name="name" class="input" value="{{ old('name', $project->name) }}" required>
                </x-field>
                <x-field label="Customer" name="customer_id" :required="true" :error="$errors->first('customer_id')">
                    <select name="customer_id" class="input" required>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id', $project->customer_id) == $c->id)>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Industry" name="industry" :required="true" :error="$errors->first('industry')">
                    <input type="text" name="industry" class="input" value="{{ old('industry', $project->industry) }}" required>
                </x-field>
                @php
                    $texts = ['location' => 'Location', 'city' => 'City', 'province' => 'Province', 'region' => 'Region'];
                @endphp
                @foreach ($texts as $key => $label)
                    <x-field :label="$label" :name="$key" :error="$errors->first($key)">
                        <input type="text" name="{{ $key }}" class="input" value="{{ old($key, $project->$key) }}">
                    </x-field>
                @endforeach
                <x-field label="Start Date" name="start_date" :required="true" :error="$errors->first('start_date')">
                    <input type="date" name="start_date" class="input" value="{{ old('start_date', $project->start_date?->toDateString()) }}" required>
                </x-field>
                <x-field label="End Date" name="end_date" :error="$errors->first('end_date')">
                    <input type="date" name="end_date" class="input" value="{{ old('end_date', $project->end_date?->toDateString()) }}">
                </x-field>
                <x-field label="Contract Value (IDR)" name="contract_value" :required="true" :error="$errors->first('contract_value')">
                    <input type="number" step="0.01" name="contract_value" class="input" value="{{ old('contract_value', $project->contract_value) }}" required>
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        @foreach (['planning', 'active', 'completed', 'on_hold', 'cancelled'] as $val)
                            <option value="{{ $val }}" @selected(old('status', $project->status) === $val)>{{ ucfirst(str_replace('_', ' ', $val)) }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Description" name="description" :error="$errors->first('description')" class="sm:col-span-2">
                    <textarea name="description" rows="3" class="input">{{ old('description', $project->description) }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.projects.show', $project->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Save Changes</button>
        </div>
    </form>
</x-layouts.app>