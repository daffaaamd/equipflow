<x-layouts.app>
    <x-slot:title>Issue Invoice</x-slot:title>
    <x-slot:subtitle>Create an invoice from an active contract</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-5">
        @csrf
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Invoice Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Contract" name="contract_id" :required="true" :error="$errors->first('contract_id')">
                    <select name="contract_id" class="input" required>
                        <option value="">Select contract</option>
                        @foreach ($contracts as $c)
                            <option value="{{ $c->id }}" @selected(old('contract_id') == $c->id)>{{ $c->contract_number }} — {{ $c->customer?->company_name }} (IDR {{ number_format($c->contract_value, 0) }})</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Invoice Date" name="invoice_date" :required="true" :error="$errors->first('invoice_date')">
                    <input type="date" name="invoice_date" class="input" value="{{ old('invoice_date', now()->toDateString()) }}" required>
                </x-field>
                <x-field label="Due Date" name="due_date" :required="true" :error="$errors->first('due_date')">
                    <input type="date" name="due_date" class="input" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}" required>
                </x-field>
                <x-field label="Period Start" name="period_start" :error="$errors->first('period_start')">
                    <input type="date" name="period_start" class="input" value="{{ old('period_start') }}">
                </x-field>
                <x-field label="Period End" name="period_end" :error="$errors->first('period_end')">
                    <input type="date" name="period_end" class="input" value="{{ old('period_end') }}">
                </x-field>
                <x-field label="Subtotal (IDR)" name="subtotal" :required="true" :error="$errors->first('subtotal')">
                    <input type="number" step="0.01" min="0" name="subtotal" class="input" value="{{ old('subtotal') }}" required>
                </x-field>
                <x-field label="Tax (IDR)" name="tax" :required="true" :error="$errors->first('tax')">
                    <input type="number" step="0.01" min="0" name="tax" class="input" value="{{ old('tax', 0) }}" required>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Issue Invoice</button>
        </div>
    </form>
</x-layouts.app>