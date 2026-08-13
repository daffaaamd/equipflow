<x-layouts.app>
    <x-slot:title>Record Payment</x-slot:title>
    <x-slot:subtitle>Apply a payment against an outstanding invoice</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Payment Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Invoice" name="invoice_id" :required="true" :error="$errors->first('invoice_id')">
                    <select name="invoice_id" id="invoice-select" class="input" required>
                        <option value="">Select invoice</option>
                        @foreach ($invoices as $inv)
                            <option value="{{ $inv->id }}"
                                data-balance="{{ $inv->balance }}"
                                @selected($selectedInvoice?->id == $inv->id)>
                                {{ $inv->invoice_number }} — {{ $inv->customer?->company_name }} (Balance: IDR {{ number_format($inv->balance, 0) }})
                            </option>
                        @endforeach
                    </select>
                </x-field>
                <div class="flex items-end">
                    <div class="w-full border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                        <p class="text-xs font-semibold uppercase tracking-widest text-charcoal-400">Selected Invoice Balance</p>
                        <p id="invoice-balance" class="mt-1 font-display text-xl font-bold text-navy-900">
                            @if ($selectedInvoice)
                                IDR {{ number_format($selectedInvoice->balance, 0) }}
                            @else
                                Select an invoice
                            @endif
                        </p>
                    </div>
                </div>
                <x-field label="Amount (IDR)" name="amount" :required="true" :error="$errors->first('amount')">
                    <input type="number" step="0.01" min="1" name="amount" class="input" value="{{ old('amount') }}" required>
                </x-field>
                <x-field label="Payment Date" name="payment_date" :required="true" :error="$errors->first('payment_date')">
                    <input type="date" name="payment_date" class="input" value="{{ old('payment_date', now()->toDateString()) }}" required>
                </x-field>
                <x-field label="Method" name="method" :required="true" :error="$errors->first('method')">
                    <select name="method" class="input" required>
                        @foreach (['bank_transfer' => 'Bank Transfer', 'cash' => 'Cash', 'cheque' => 'Cheque', 'giro' => 'Giro'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('method') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Reference" name="reference" :error="$errors->first('reference')">
                    <input type="text" name="reference" class="input" value="{{ old('reference') }}" placeholder="e.g. Transfer receipt no.">
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.payments.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Record Payment</button>
        </div>
    </form>

    @push('scripts')
    <script>
        const invSelect = document.getElementById('invoice-select');
        const invBalance = document.getElementById('invoice-balance');
        invSelect?.addEventListener('change', () => {
            const opt = invSelect.options[invSelect.selectedIndex];
            invBalance.textContent = opt?.dataset.balance ? 'IDR ' + Number(opt.dataset.balance).toLocaleString('id-ID') : 'Select an invoice';
        });
    </script>
    @endpush
</x-layouts.app>