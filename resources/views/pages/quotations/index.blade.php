<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $base = $isCustomer ? 'customer' : 'admin';
        $badges = ['sent' => 'blue', 'accepted' => 'green', 'revision' => 'amber', 'rejected' => 'red', 'expired' => 'gray'];
    @endphp

    <x-slot:title>{{ $isCustomer ? 'My Quotations' : 'Quotations' }}</x-slot:title>
    <x-slot:subtitle>{{ $isCustomer ? 'Review and respond to quotations' : 'Generate and manage quotations' }}</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route("$base.quotations.index") }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search quotation number…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['sent', 'accepted', 'revision', 'rejected'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route("$base.quotations.index") }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            @if (!$isCustomer)
                <a href="{{ route('admin.quotations.create') }}" class="btn-brand btn-md">+ New Quotation</a>
            @endif
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Quotation</th><th>Customer</th><th>Valid Until</th><th>Grand Total</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($quotations as $q)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $q->quotation_number }}</td>
                            <td>{{ $q->customer?->company_name }}</td>
                            <td>{{ $q->valid_until?->format('d M Y') }}</td>
                            <td class="font-semibold">IDR {{ number_format($q->grand_total, 0) }}</td>
                            <td><x-badge type="{{ $badges[$q->status] ?? 'gray' }}">{{ ucfirst($q->status) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route("$base.quotations.show", $q->id) }}" class="btn-outline btn-sm">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-charcoal-400">No quotations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$quotations" /></div>
    </div>
</x-layouts.app>