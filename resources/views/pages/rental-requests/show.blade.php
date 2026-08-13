<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $base = $isCustomer ? 'customer' : 'admin';
        $badges = ['pending' => 'amber', 'reviewed' => 'blue', 'quoted' => 'navy', 'approved' => 'green', 'rejected' => 'red', 'cancelled' => 'gray'];
    @endphp

    <x-slot:title>{{ $rentalRequest->request_number }}</x-slot:title>
    <x-slot:subtitle>{{ $rentalRequest->project_name }}</x-slot:subtitle>

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <x-badge type="{{ $badges[$rentalRequest->status] ?? 'gray' }}" class="!text-sm !px-3 !py-1">{{ ucfirst($rentalRequest->status) }}</x-badge>
            @if ($rentalRequest->created_at)
                <x-badge type="outline" class="!text-sm !px-3 !py-1">Submitted {{ $rentalRequest->created_at->format('d M Y') }}</x-badge>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            @if (!$isCustomer)
                <a href="{{ route('admin.rental-requests.edit', $rentalRequest->id) }}" class="btn-outline btn-md">Edit</a>
                @if (in_array($rentalRequest->status, ['reviewed', 'quoted']))
                    <a href="{{ route('admin.quotations.create', ['request' => $rentalRequest->id]) }}" class="btn-brand btn-md">Create Quotation</a>
                @endif
                <form method="POST" action="{{ route('admin.rental-requests.destroy', $rentalRequest->id) }}" data-confirm="Delete this request?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-md">Delete</button>
                </form>
            @elseif ($rentalRequest->quotation)
                <a href="{{ route('customer.quotations.show', $rentalRequest->quotation->id) }}" class="btn-brand btn-md">View Quotation</a>
            @endif
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Requested Items</h3></div>
                <div class="table-wrap">
                    <table class="table-base">
                        <thead><tr><th>Equipment</th><th>Category</th><th>Qty</th><th>Start</th><th>End</th></tr></thead>
                        <tbody>
                            @forelse ($rentalRequest->items as $item)
                                <tr>
                                    <td class="font-semibold text-navy-900">
                                        @if ($item->equipment)
                                            <a href="{{ route('catalog.show', $item->equipment_id) }}" class="hover:text-brand-500">{{ $item->equipment->name }}</a>
                                        @else
                                            Specific unit to be advised
                                        @endif
                                    </td>
                                    <td>{{ $item->category?->name ?? '—' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->start_date?->format('d M Y') }}</td>
                                    <td>{{ $item->end_date?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No items</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Service Requirements</h3></div>
                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    @php
                        $services = [
                            ['Operator Required', $rentalRequest->operator_required],
                            ['Transportation Included', $rentalRequest->transportation_included],
                            ['Fuel Included', $rentalRequest->fuel_included],
                        ];
                    @endphp
                    @foreach ($services as $s)
                        <p class="flex items-center justify-between rounded-sm border border-charcoal-200 px-4 py-3 text-sm">
                            <span class="text-charcoal-500">{{ $s[0] }}</span>
                            <span class="font-semibold {{ $s[1] ? 'text-green-600' : 'text-red-500' }}">{{ $s[1] ? 'Yes' : 'No' }}</span>
                        </p>
                    @endforeach
                    @if ($rentalRequest->additional_requirements)
                        <p class="sm:col-span-2"><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400 mb-1">Additional Requirements</span>{{ $rentalRequest->additional_requirements }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card">
                <div class="card-header"><h3 class="text-lg font-semibold">Customer</h3></div>
                <div class="space-y-3 p-6 text-sm">
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Company</span><a href="{{ $isCustomer ? '#' : route('admin.customers.show', $rentalRequest->customer_id) }}" class="font-semibold text-navy-900">{{ $rentalRequest->customer?->company_name }}</a></p>
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Contact</span>{{ $rentalRequest->contact_person }}</p>
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Phone</span>{{ $rentalRequest->contact_phone ?? '—' }}</p>
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Project Type</span>{{ $rentalRequest->project_type ?? '—' }}</p>
                    <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Location</span>{{ $rentalRequest->project_location ?? '—' }}</p>
                    @if ($rentalRequest->reviewed_at)
                        <p><span class="block text-xs font-semibold uppercase tracking-widest text-charcoal-400">Reviewed At</span>{{ $rentalRequest->reviewed_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>

            @if (!$isCustomer)
                <div class="card">
                    <div class="card-header"><h3 class="text-lg font-semibold">Update Status</h3></div>
                    <form method="POST" action="{{ route('admin.rental-requests.update', $rentalRequest->id) }}" class="space-y-3 p-6">
                        @csrf @method('PUT')
                        <select name="status" class="input">
                            @foreach (['pending', 'reviewed', 'quoted', 'approved', 'rejected', 'cancelled'] as $val)
                                <option value="{{ $val }}" @selected($rentalRequest->status === $val)>{{ ucfirst($val) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-navy btn-md w-full">Update Status</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>