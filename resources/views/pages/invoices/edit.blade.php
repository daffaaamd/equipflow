<x-layouts.app>
    <x-slot:title>Edit Invoice</x-slot:title>
    <x-slot:subtitle>{{ $invoice->invoice_number }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.invoices.update', $invoice->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Invoice Details</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2 border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                    <p class="font-semibold text-navy-900">{{ $invoice->invoice_number }} — {{ $invoice->customer?->company_name }}</p>
                    <p>Contract: {{ $invoice->contract?->contract_number ?? '—' }} · Project: {{ $invoice->project?->name ?? '—' }}</p>
                </div>
                <x-field label="Invoice Date" name="invoice_date" :required="true" :error="$errors->first('invoice_date')">
                    <input type="date" name="invoice_date" class="input" value="{{ old('invoice_date', $invoice->invoice_date->toDateString()) }}" required>
                </x-field>
                <x-field label="Due Date" name="due_date" :required="true" :error="$errors->first('due_date')">
                    <input type="date" name="due_date" class="input" value="{{ old('due_date', $invoice->due_date->toDateString()) }}" required>
                </x-field>
                <x-field label="Subtotal (IDR)" name="subtotal" :required="true" :error="$errors->first('subtotal')">
                    <input type="number" step="0.01" min="0" name="subtotal" class="input" value="{{ old('subtotal', $invoice->subtotal) }}" required>
                </x-field>
                <x-field label="Tax (IDR)" name="tax" :required="true" :error="$errors->first('tax')">
                    <input type="number" step="0.01" min="0" name="tax" class="input" value="{{ old('tax', $invoice->tax) }}" required>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')" class="sm:col-span-2">
                    <textarea name="notes" rows="3" class="input">{{ old('notes', $invoice->notes) }}</textarea>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Invoice</button>
        </div>
    </form>
</x-layouts.app>