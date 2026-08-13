<x-layouts.app>
    <x-slot:title>Update Quotation</x-slot:title>
    <x-slot:subtitle>{{ $quotation->quotation_number }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.quotations.update', $quotation->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Quotation Status</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        @foreach (['accepted' => 'Accepted', 'revision' => 'Revision Requested', 'rejected' => 'Rejected'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $quotation->status) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
                <div class="flex items-end">
                    <div class="w-full border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                        <p class="font-semibold text-navy-900">{{ $quotation->quotation_number }}</p>
                        <p>Grand Total: <strong>IDR {{ number_format($quotation->grand_total, 0) }}</strong></p>
                        <p class="text-xs">Customer: {{ $quotation->customer?->company_name }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Status</button>
        </div>
    </form>
</x-layouts.app>