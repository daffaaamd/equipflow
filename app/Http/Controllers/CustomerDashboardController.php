<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\RentalRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer;

        $activeRentals = $customer ? Contract::where('customer_id', $customer->id)->where('status', 'active')->count() : 0;
        $pendingRequests = $customer ? RentalRequest::where('customer_id', $customer->id)->whereIn('status', ['pending', 'reviewed'])->count() : 0;
        $activeProjects = $customer ? $customer->projects()->where('status', 'active')->count() : 0;
        $outstanding = $customer ? $customer->outstanding : 0;
        $totalRentalValue = $customer ? $customer->total_rental_value : 0;

        $upcomingDeliveries = $customer
            ? \App\Models\Delivery::where('customer_id', $customer->id)
                ->whereNotIn('status', ['delivered', 'confirmed'])
                ->whereDate('delivery_date', '>=', now())
                ->with('equipment')->latest()->take(5)->get()
            : collect();

        $rentalHistory = $customer
            ? Contract::where('customer_id', $customer->id)->with('items.equipment')->latest()->take(6)->get()
            : collect();

        $recentRequests = $customer
            ? RentalRequest::where('customer_id', $customer->id)->with('items')->latest()->take(5)->get()
            : collect();

        $invoices = $customer
            ? Invoice::where('customer_id', $customer->id)->latest()->take(5)->get()
            : collect();

        return view('pages.customer.dashboard', compact(
            'customer', 'activeRentals', 'pendingRequests', 'activeProjects',
            'outstanding', 'totalRentalValue', 'upcomingDeliveries', 'rentalHistory',
            'recentRequests', 'invoices',
        ));
    }
}