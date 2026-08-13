<x-layouts.app>
    <x-slot:title>Create Quotation</x-slot:title>
    <x-slot:subtitle>Generate a quotation from a rental request</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.quotations.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Reference</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Rental Request" name="rental_request_id" :required="true" :error="$errors->first('rental_request_id')">
                    <select name="rental_request_id" id="request-select" class="input" required>
                        <option value="">Select request</option>
                        @foreach ($requests as $r)
                            <option value="{{ $r->id }}"
                                data-start="{{ $r->earliest_start?->toDateString() }}"
                                data-end="{{ $r->latest_end?->toDateString() }}"
                                @selected($selectedRequest?->id == $r->id)>
                                {{ $r->request_number }} — {{ $r->customer?->company_name }} ({{ $r->project_name }})
                            </option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Valid Until" name="valid_until" :required="true" :error="$errors->first('valid_until')">
                    <input type="date" name="valid_until" class="input" value="{{ old('valid_until', now()->addDays(14)->toDateString()) }}" required>
                </x-field>
                <x-field label="Rental Period Start" name="rental_period_start" :required="true" :error="$errors->first('rental_period_start')">
                    <input type="date" name="rental_period_start" id="period-start" class="input" value="{{ old('rental_period_start') }}" required>
                </x-field>
                <x-field label="Rental Period End" name="rental_period_end" :required="true" :error="$errors->first('rental_period_end')">
                    <input type="date" name="rental_period_end" id="period-end" class="input" value="{{ old('rental_period_end') }}" required>
                </x-field>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Equipment & Pricing</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-field label="Equipment" name="equipment_id" :required="true" :error="$errors->first('equipment_id')">
                    <select name="equipment_id" id="equipment-select" class="input" required>
                        <option value="">Select equipment</option>
                        @foreach ($equipment as $eq)
                            <option value="{{ $eq->id }}"
                                data-rate="{{ $eq->daily_rate }}"
                                @selected(old('equipment_id') == $eq->id)>
                                {{ $eq->equipment_code }} — {{ $eq->brand }} {{ $eq->model }} (IDR {{ number_format($eq->daily_rate, 0) }}/day)
                            </option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Quantity" name="quantity" :required="true" :error="$errors->first('quantity')">
                    <input type="number" name="quantity" id="quantity" class="input" value="{{ old('quantity', 1) }}" min="1" required>
                </x-field>
                <x-field label="Unit Rate (IDR/day)" name="unit_rate" :required="true" :error="$errors->first('unit_rate')">
                    <input type="number" step="0.01" name="unit_rate" id="unit-rate" class="input" value="{{ old('unit_rate') }}" required>
                </x-field>
                <x-field label="Operator Cost" name="operator_cost" :error="$errors->first('operator_cost')">
                    <input type="number" step="0.01" name="operator_cost" class="input" value="{{ old('operator_cost', 0) }}">
                </x-field>
                <x-field label="Transportation Cost" name="transportation_cost" :error="$errors->first('transportation_cost')">
                    <input type="number" step="0.01" name="transportation_cost" class="input" value="{{ old('transportation_cost', 0) }}">
                </x-field>
                <x-field label="Fuel Cost" name="fuel_cost" :error="$errors->first('fuel_cost')">
                    <input type="number" step="0.01" name="fuel_cost" class="input" value="{{ old('fuel_cost', 0) }}">
                </x-field>
                <x-field label="Additional Service Cost" name="additional_service_cost" :error="$errors->first('additional_service_cost')">
                    <input type="number" step="0.01" name="additional_service_cost" class="input" value="{{ old('additional_service_cost', 0) }}">
                </x-field>
                <x-field label="Discount" name="discount" :error="$errors->first('discount')">
                    <input type="number" step="0.01" name="discount" class="input" value="{{ old('discount', 0) }}">
                </x-field>
                <x-field label="Tax Rate (%)" name="tax_rate" :error="$errors->first('tax_rate')">
                    <input type="number" step="0.01" name="tax_rate" class="input" value="{{ old('tax_rate', 11) }}" min="0" max="100">
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2 lg:col-span-3">
                    <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.quotations.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Generate Quotation</button>
        </div>
    </form>

    @push('scripts')
    <script>
        const reqSelect = document.getElementById('request-select');
        const periodStart = document.getElementById('period-start');
        const periodEnd = document.getElementById('period-end');
        reqSelect?.addEventListener('change', () => {
            const opt = reqSelect.options[reqSelect.selectedIndex];
            if (opt?.dataset.start && !periodStart.value) periodStart.value = opt.dataset.start;
            if (opt?.dataset.end && !periodEnd.value) periodEnd.value = opt.dataset.end;
        });
        const eqSelect = document.getElementById('equipment-select');
        const unitRate = document.getElementById('unit-rate');
        eqSelect?.addEventListener('change', () => {
            const opt = eqSelect.options[eqSelect.selectedIndex];
            if (opt?.dataset.rate && !unitRate.value) unitRate.value = opt.dataset.rate;
        });
    </script>
    @endpush
</x-layouts.app>