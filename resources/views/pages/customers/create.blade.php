<x-layouts.app>
    <x-slot:title>Add Customer</x-slot:title>
    <x-slot:subtitle>Create a new customer account</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Company Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Company Name" name="company_name" :required="true" :error="$errors->first('company_name')">
                    <input type="text" name="company_name" class="input" value="{{ old('company_name') }}" required>
                </x-field>
                <x-field label="Industry" name="industry" :error="$errors->first('industry')">
                    <input type="text" name="industry" class="input" value="{{ old('industry') }}" placeholder="Construction / Mining…">
                </x-field>
                <x-field label="Contact Person" name="contact_person" :required="true" :error="$errors->first('contact_person')">
                    <input type="text" name="contact_person" class="input" value="{{ old('contact_person') }}" required>
                </x-field>
                <x-field label="Email" name="email" :required="true" :error="$errors->first('email')">
                    <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                </x-field>
                <x-field label="Phone" name="phone" :error="$errors->first('phone')">
                    <input type="text" name="phone" class="input" value="{{ old('phone') }}">
                </x-field>
                <x-field label="Tax ID (NPWP)" name="tax_id" :error="$errors->first('tax_id')">
                    <input type="text" name="tax_id" class="input" value="{{ old('tax_id') }}">
                </x-field>
                <x-field label="Segment" name="segment" :error="$errors->first('segment')">
                    <select name="segment" class="input">
                        <option value="">Select segment</option>
                        @foreach (['strategic' => 'Strategic', 'high_value' => 'High Value', 'medium_value' => 'Medium Value', 'low_value' => 'Low Value'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('segment') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        <option value="active" @selected(old('status') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                </x-field>
                <x-field label="Address" name="address" :error="$errors->first('address')" class="sm:col-span-2">
                    <textarea name="address" rows="2" class="input">{{ old('address') }}</textarea>
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
                <x-field label="Notes" name="notes" :error="$errors->first('notes')">
                    <textarea name="notes" rows="2" class="input">{{ old('notes') }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Portal Access</h3>
                <p class="text-sm text-charcoal-400">Optionally create a customer login</p>
            </div>
            <div class="p-6">
                <label class="flex items-center gap-3 text-sm font-medium text-charcoal-700">
                    <input type="checkbox" name="create_login" value="1" class="h-4 w-4 accent-brand-500" @checked(old('create_login'))>
                    Create portal login for this customer
                </label>
                <div class="mt-4 grid gap-4 sm:grid-cols-2" id="login-fields" style="{{ old('create_login') ? '' : 'display:none' }}">
                    <x-field label="Initial Password" name="password" :error="$errors->first('password')">
                        <input type="text" name="password" class="input" value="{{ old('password', 'password123') }}" placeholder="Min. 8 characters">
                    </x-field>
                    <p class="flex items-end pb-2 text-xs text-charcoal-400">The portal uses the customer email as the username.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Create Customer</button>
        </div>
    </form>

    @push('scripts')
    <script>
        const loginCheck = document.querySelector('input[name="create_login"]');
        loginCheck?.addEventListener('change', () => {
            document.getElementById('login-fields').style.display = loginCheck.checked ? '' : 'none';
        });
    </script>
    @endpush
</x-layouts.app>