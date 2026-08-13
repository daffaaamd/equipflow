<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('pages.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'customer',
            'company_name' => $data['company_name'],
            'phone' => $data['phone'],
            'is_active' => true,
        ]);

        Customer::create([
            'customer_code' => 'CUS-' . str_pad((string) ($user->id + 50), 4, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'contact_person' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => 'active',
        ]);

        Auth::login($user);
        ActivityService::log('register', 'user', $user->id, 'New customer account registered');

        return redirect()->route('customer.dashboard');
    }
}