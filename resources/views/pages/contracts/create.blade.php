<x-layouts.app>
    <x-slot:title>Create Contract</x-slot:title>
    <x-slot:subtitle>Convert an accepted quotation into a contract</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.contracts.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Quotation</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Accepted Quotation" name="quotation_id" :required="true" :error="$errors->first('quotation_id')">
                    <select name="quotation_id" id="quotation-select" class="input" required>
                        <option value="">Select quotation</option>
                        @foreach ($quotations as $q)
                            <option value="{{ $q->id }}"
                                data-value="{{ $q->grand_total }}"
                                data-start="{{ $q->rental_period_start?->toDateString() }}"
                                data-end="{{ $q->rental_period_end?->toDateString() }}"
                                @selected($selectedQuotation?->id == $q->id)>
                                {{ $q->quotation_number }} — {{ $q->customer?->company_name }} (IDR {{ number_format($q->grand_total, 0) }})
                            </option>
                        @endforeach
                    </select>
                </x-field>
                <div class="flex items-end">
                    <div class="w-full border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                        <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">Selected Quotation</p>
                        <p id="quotation-summary" class="mt-1 text-charcoal-700">
                            @if ($selectedQuotation)
                                {{ $selectedQuotation->quotation_number }} · IDR {{ number_format($selectedQuotation->grand_total, 0) }}
                            @else
                                Select a quotation to preview
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Contract Terms</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Start Date" name="start_date" :required="true" :error="$errors->first('start_date')">
                    <input type="date" name="start_date" id="start-date" class="input" value="{{ old('start_date') }}" required>
                </x-field>
                <x-field label="End Date" name="end_date" :required="true" :error="$errors->first('end_date')">
                    <input type="date" name="end_date" id="end-date" class="input" value="{{ old('end_date') }}" required>
                </x-field>
                <x-field label="Rental Rate (IDR)" name="rental_rate" :required="true" :error="$errors->first('rental_rate')">
                    <input type="number" step="0.01" name="rental_rate" id="rental-rate" class="input" value="{{ old('rental_rate') }}" required>
                </x-field>
                <x-field label="Deposit (IDR)" name="deposit" :error="$errors->first('deposit')">
                    <input type="number" step="0.01" name="deposit" class="input" value="{{ old('deposit', 0) }}">
                </x-field>
                <x-field label="Payment Terms" name="payment_terms" :error="$errors->first('payment_terms')">
                    <input type="text" name="payment_terms" class="input" value="{{ old('payment_terms', '30 days') }}">
                </x-field>
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                        <option value="active" @selected(old('status') === 'active')>Active (signed)</option>
                    </select>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                </x-field>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.contracts.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Create Contract</button>
        </div>
    </form>

    @push('scripts')
    <script>
        const qSelect = document.getElementById('quotation-select');
        const summary = document.getElementById('quotation-summary');
        const startDate = document.getElementById('start-date');
        const endDate = document.getElementById('end-date');
        const rentalRate = document.getElementById('rental-rate');
        qSelect?.addEventListener('change', () => {
            const opt = qSelect.options[qSelect.selectedIndex];
            if (opt?.dataset.value) {
                summary.textContent = opt.textContent.trim().split(' — ')[0] + ' · ' + opt.dataset.value;
                if (!rentalRate.value) rentalRate.value = opt.dataset.value;
                if (opt.dataset.start && !startDate.value) startDate.value = opt.dataset.start;
                if (opt.dataset.end && !endDate.value) endDate.value = opt.dataset.end;
            }
        });
    </script>
    @endpush
</x-layouts.app>