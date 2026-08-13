<x-layouts.app>
    <x-slot:title>Edit Contract</x-slot:title>
    <x-slot:subtitle>{{ $contract->contract_number }}</x-slot:subtitle>

    <form method="POST" action="{{ route('admin.contracts.update', $contract->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Contract Status</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input" required>
                        @foreach (['draft', 'active', 'completed', 'terminated'] as $val)
                            <option value="{{ $val }}" @selected(old('status', $contract->status) === $val)>{{ ucfirst($val) }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field label="Notes" name="notes" :error="$errors->first('notes')">
                    <input type="text" name="notes" class="input" value="{{ old('notes', $contract->notes) }}">
                </x-field>
                <div class="sm:col-span-2 border border-charcoal-200 bg-charcoal-50 p-4 text-sm text-charcoal-600">
                    <p class="font-semibold text-navy-900">{{ $contract->contract_number }} — {{ $contract->customer?->company_name }}</p>
                    <p>Value: <strong>IDR {{ number_format($contract->contract_value, 0) }}</strong> · Period: {{ $contract->start_date->format('d M Y') }} — {{ $contract->end_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Contract</button>
        </div>
    </form>
</x-layouts.app>