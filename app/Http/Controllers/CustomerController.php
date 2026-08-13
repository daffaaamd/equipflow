<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%{$s}%")
                    ->orWhere('customer_code', 'like', "%{$s}%")
                    ->orWhere('contact_person', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            }))
            ->when($request->segment, fn ($q, $v) => $q->where('segment', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->industry, fn ($q, $v) => $q->where('industry', $v))
            ->withCount(['contracts' => fn ($q) => $q->where('status', 'active')])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'segment', 'status', 'industry']);

        return view('pages.customers.index', compact('customers', 'filters'));
    }

    public function create(): View
    {
        return view('pages.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $user = null;
        if ($request->create_login && $request->filled('email')) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['contact_person'],
                    'password' => $request->input('password', 'password123'),
                    'role' => 'customer',
                    'company_name' => $data['company_name'],
                    'phone' => $data['phone'],
                ]
            );
        }

        $customer = Customer::create(array_merge($data, [
            'customer_code' => 'CUS-' . str_pad((string) (Customer::max('id') + 51), 4, '0', STR_PAD_LEFT),
            'user_id' => $user?->id,
        ]));

        ActivityService::log('create', 'customer', $customer->id, "Customer {$customer->company_name} created");

        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load('projects', 'contracts', 'rentalRequests', 'invoices');

        return view('pages.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('pages.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validateData($request);
        $customer->update($data);
        ActivityService::log('update', 'customer', $customer->id, "Customer {$customer->company_name} updated");

        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        ActivityService::log('delete', 'customer', $customer->id, "Customer {$customer->company_name} removed");
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer removed successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'industry' => ['nullable', 'string', 'max:100'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'segment' => ['nullable', 'in:strategic,high_value,medium_value,low_value'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
