<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account — EquipFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-navy-950">

    <div class="flex min-h-screen">
        <div class="relative hidden w-1/2 lg:block">
            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e788b0" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="" class="h-full w-full object-cover opacity-35">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/60 to-navy-950/30"></div>
            <div class="absolute inset-0 flex flex-col justify-between p-12">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                    <span class="font-display text-2xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
                </a>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-400">Customer Portal</p>
                    <h1 class="mt-4 max-w-md font-display text-4xl font-bold uppercase leading-[1.05] tracking-tight text-white">
                        Rent Equipment, Track Everything.
                    </h1>
                    <ul class="mt-6 space-y-3 text-sm text-charcoal-300">
                        @php
                            $benefits = ['Submit rental requests in minutes', 'Review quotations & sign contracts online', 'Track deliveries and view invoices'];
                        @endphp
                        @foreach ($benefits as $b)
                            <li class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {{ $b }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex w-full items-center justify-center px-6 py-16 lg:w-1/2">
            <div class="w-full max-w-md">
                <a href="{{ route('landing') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center bg-brand-500 font-display text-lg font-bold text-white">E</span>
                    <span class="font-display text-2xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
                </a>

                <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-white">Create Account</h2>
                <p class="mt-2 text-sm text-charcoal-400">Register to access the customer portal.</p>

                <x-alert type="error" />

                <form method="POST" action="{{ route('register.attempt') }}" class="mt-8 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="label !text-charcoal-300">Full Name</label>
                            <input type="text" name="name" id="name" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div>
                            <label for="company_name" class="label !text-charcoal-300">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" value="{{ old('company_name') }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="label !text-charcoal-300">Email Address</label>
                        <input type="email" name="email" id="email" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" value="{{ old('email') }}" placeholder="you@company.com" required>
                    </div>
                    <div>
                        <label for="phone" class="label !text-charcoal-300">Phone (optional)</label>
                        <input type="text" name="phone" id="phone" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" value="{{ old('phone') }}" placeholder="+62 ...">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="label !text-charcoal-300">Password</label>
                            <input type="password" name="password" id="password" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" placeholder="Min. 8 characters" required>
                        </div>
                        <div>
                            <label for="password_confirmation" class="label !text-charcoal-300">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" placeholder="Repeat password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-brand btn-lg mt-2 w-full">Create Account</button>
                </form>

                <p class="mt-6 text-center text-sm text-charcoal-400">
                    Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-400 hover:text-brand-300">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
