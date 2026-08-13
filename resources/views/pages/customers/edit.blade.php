<x-layouts.app>
    <x-slot:title>Edit Customer</x-slot:title>
    <x-slot:subtitle>{{ $customer->company_name }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Company Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                @php
                    $fields = [
                        'company_name' => ['Company Name', 'text', true], 'industry' => ['Industry', 'text', false],
                        'contact_person' => ['Contact Person', 'text', true], 'email' => ['Email', 'email', true],
                        'phone' => ['Phone', 'text', false], 'tax_id' => ['Tax ID (NPWP)', 'text', false],
                        'city' => ['City', 'text', false], 'province' => ['Province', 'text', false],
                        'region' => ['Region', 'text', false],
                    ];
                @endphp
                @foreach ($fields as $key => [$label, $type, $req])
                    <x-field :label="$label" :name="$key" :required="$req" :error="$errors->first($key)">
                        <input type="{{ $type }}" name="{{ $key }}" class="input" value="{{ old($key, $customer->$key) }}" @if($req) required @endif>
                    </x-field>
                @endforeach
                <x-field label="Segment" name="segment" :error="$errors->first('segment')">
                    <select name="segment" class="input">
                        <option value="">Select segment</option>
                        @foreach (['strategic' => 'Strategic', 'high_value' => 'High Value', 'medium_value' => 'Medium Value', 'low_value' => 'Low Value'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('segment', $customer->segment) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        <option value="active" @selected(old('status', $customer->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $customer->status) === 'inactive')>Inactive</option>
                    </select>
                </x-field>
                <x-field label="Address" name="address" :error="$errors->first('address')" class="sm:col-span-2">
                    <textarea name="address" rows="2" class="input">{{ old('address', $customer->address) }}</textarea>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="2" class="input">{{ old('notes', $customer->notes) }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Save Changes</button>
        </div>
    </form>
</x-layouts.app>