<x-layouts.app>
    <x-slot:title>Edit Payment</x-slot:title>
    <x-slot:subtitle>{{ $payment->payment_number }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Payment Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2 border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                    <p class="font-semibold text-navy-900">{{ $payment->payment_number }} — IDR {{ number_format($payment->amount, 0) }}</p>
                    <p>Invoice: {{ $payment->invoice?->invoice_number ?? '—' }} · Customer: {{ $payment->customer?->company_name }}</p>
                </div>
                <x-field label="Payment Date" name="payment_date" :required="true" :error="$errors->first('payment_date')">
                    <input type="date" name="payment_date" class="input" value="{{ old('payment_date', $payment->payment_date->toDateString()) }}" required>
                </x-field>
                <x-field label="Method" name="method" :required="true" :error="$errors->first('method')">
                    <select name="method" class="input" required>
                        @foreach (['bank_transfer' => 'Bank Transfer', 'cash' => 'Cash', 'cheque' => 'Cheque', 'giro' => 'Giro'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('method', $payment->method) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Reference" name="reference" :error="$errors->first('reference')">
                    <input type="text" name="reference" class="input" value="{{ old('reference', $payment->reference) }}">
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes', $payment->notes) }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Payment</button>
        </div>
    </form>
</x-layouts.app>