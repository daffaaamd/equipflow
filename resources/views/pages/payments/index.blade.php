<x-layouts.app>
    @php
        $methodBadges = ['bank_transfer' => 'blue', 'cash' => 'green', 'cheque' => 'amber', 'giro' => 'navy'];
    @endphp

    <x-slot:title>Payments</x-slot:title>
    <x-slot:subtitle>Record and track incoming payments</x-slot:subtitle>

    <div class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input !w-56" placeholder="Search payment number…">
                <select name="customer_id" class="input !w-56" onchange="this.form.submit()">
                    <option value="">All Customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" @selected(($filters['customer_id'] ?? '') == $c->id)>{{ $c->company_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-navy btn-md">Filter</button>
                @if (count(array_filter($filters)))
                    <a href="{{ route('admin.payments.index') }}" class="btn-outline btn-md">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.payments.create') }}" class="btn-brand btn-md">+ Record Payment</a>
        </div>

        <div class="table-wrap">
            <table class="table-base">
                <thead><tr><th>Payment</th><th>Customer</th><th>Invoice</th><th>Date</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($payments as $p)
                        <tr>
                            <td class="font-semibold text-navy-900">{{ $p->payment_number }}</td>
                            <td>{{ $p->customer?->company_name }}</td>
                            <td class="text-xs">{{ $p->invoice?->invoice_number ?? '—' }}</td>
                            <td>{{ $p->payment_date->format('d M Y') }}</td>
                            <td><x-badge type="{{ $methodBadges[$p->method] ?? 'gray' }}">{{ ucfirst(str_replace('_', ' ', $p->method)) }}</x-badge></td>
                            <td class="text-xs">{{ $p->reference ?? '—' }}</td>
                            <td class="text-right font-semibold text-green-600">IDR {{ number_format($p->amount, 0) }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.payments.show', $p->id) }}" class="btn-outline btn-sm">View</a>
                                    <a href="{{ route('admin.payments.edit', $p->id) }}" class="btn-navy btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-charcoal-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><x-pagination :links="$payments" /></div>
    </div>
</x-layouts.app>