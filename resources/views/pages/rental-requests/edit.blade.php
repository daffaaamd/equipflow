<x-layouts.app>
    <x-slot:title>Edit Rental Request</x-slot:title>
    <x-slot:subtitle>{{ $rentalRequest->request_number }}</x-slot:subtitle>

    <div class="card mb-5 border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
        Status changes trigger notifications to the customer. Use the <strong>Update Status</strong> panel on the detail page for workflow changes; edit project details here.
    </div>

    <form method="POST" action="{{ route('admin.rental-requests.update', $rentalRequest->id) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-header bg-navy-950">
                <h3 class="font-display text-lg font-bold uppercase tracking-wide text-white">Request Status</h3>
            </div>
            <div class="p-6">
                <x-field label="Status" name="status" :required="true" :error="$errors->first('status')">
                    <select name="status" class="input">
                        @foreach (['pending', 'reviewed', 'quoted', 'approved', 'rejected', 'cancelled'] as $val)
                            <option value="{{ $val }}" @selected(old('status', $rentalRequest->status) === $val)>{{ ucfirst($val) }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.rental-requests.show', $rentalRequest->id) }}" class="btn-outline btn-lg">Cancel</a>
            <button type="submit" class="btn-brand btn-lg">Update Status</button>
        </div>
    </form>
</x-layouts.app>