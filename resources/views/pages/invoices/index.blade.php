<x-layouts.app>
    @php
        $isCustomer = auth()->user()->role === 'customer';
        $badges = ['pending' => 'amber', 'partial' => 'blue', 'paid' => 'green', 'overdue' => 'red'];
    @endphp

    <x-slot:title>{{ $isCustomer ? 'My Invoices' : 'Invoices' }}</x-slot:title>
    <x-slot:subtitle>{{ $isCustomer ? 'Track your billing and payments' : 'Issue and manage invoices' }}</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route(($isCustomer ? 'customer' : 'admin') . '.invoices.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search invoice number…">
                <select name="status" class="input !w-36" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'partial', 'paid', 'overdue'] as $val)
                        <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ ucfirst($val) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route(($isCustomer ? 'customer' : 'admin') . '.invoices.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            @if (!$isCustomer)
                <a href="{{ route('admin.invoices.create') }}" class="btn-brand btn-md">+ Issue Invoice</a>
            @endif
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Due</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($invoices as $inv)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->customer?->company_name }}</td>
                            <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td class="text-xs">{{ $inv->due_date->format('d M Y') }}</td>
                            <td class="font-semibold">IDR {{ number_format($inv->total, 0) }}</td>
                            <td>IDR {{ number_format($inv->amount_paid, 0) }}</td>
                            <td class="{{ $inv->balance > 0 ? 'text-red-600' : 'text-green-600' }}">IDR {{ number_format($inv->balance, 0) }}</td>
                            <td><x-badge type="{{ $badges[$inv->payment_status] ?? 'gray' }}">{{ ucfirst($inv->payment_status) }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route(($isCustomer ? 'customer' : 'admin') . '.invoices.show', $inv->id) }}" class="btn-outline btn-sm">View</a>
                                    @if (!$isCustomer)
                                        <a href="{{ route('admin.invoices.edit', $inv->id) }}" class="btn-navy btn-sm">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-10 text-center text-charcoal-400">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$invoices" /></div>
    </div>
</x-layouts.app>