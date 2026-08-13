<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — EquipFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-navy-950">

    <div class="flex min-h-screen">
        {{-- Brand panel --}}
        <div class="relative hidden w-1/2 lg:block">
            <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5" onerror="this.onerror=null;this.src='/img/placeholder.svg'" alt="" class="h-full w-full object-cover opacity-35">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/60 to-navy-950/30"></div>
            <div class="absolute inset-0 flex flex-col justify-between p-12">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                    <span class="font-display text-2xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
                </a>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-400">Heavy Equipment Rental</p>
                    <h1 class="mt-4 max-w-md font-display text-4xl font-bold uppercase leading-[1.05] tracking-tight text-white">
                        Power Your Project. We Move the Earth.
                    </h1>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-charcoal-300">
                        Manage your fleet, track rentals, review quotations, and monitor your operations from one dashboard.
                    </p>
                </div>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex w-full items-center justify-center px-6 py-16 lg:w-1/2">
            <div class="w-full max-w-md">
                <a href="{{ route('landing') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center bg-brand-500 font-display text-lg font-bold text-white">E</span>
                    <span class="font-display text-2xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
                </a>

                <h2 class="font-display text-3xl font-bold uppercase tracking-tight text-white">Sign In</h2>
                <p class="mt-2 text-sm text-charcoal-400">Access your EquipFlow dashboard.</p>

                <x-alert type="error" />

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="label !text-charcoal-300">Email Address</label>
                        <input type="email" name="email" id="email" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
                    </div>
                    <div>
                        <label for="password" class="label !text-charcoal-300">Password</label>
                        <input type="password" name="password" id="password" class="input !border-navy-700 !bg-navy-900 !text-white placeholder:!text-charcoal-500" placeholder="••••••••" required>
                    </div>
                    <div class="flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <label class="flex items-center gap-2 text-sm text-charcoal-300">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded-sm accent-brand-500">
                            Remember me
                        </label>
                        <span class="text-sm text-charcoal-500">Password: <code class="text-charcoal-300">password</code></span>
                    </div>
                    <button type="submit" class="btn-brand btn-lg w-full">Sign In</button>
                </form>

                <p class="mt-6 text-center text-sm text-charcoal-400">
                    New customer? <a href="{{ route('register') }}" class="font-semibold text-brand-400 hover:text-brand-300">Create an account</a>
                </p>

                <div class="mt-8 border-t border-navy-800 pt-6">
                    <p class="text-center text-[11px] font-semibold uppercase tracking-widest text-charcoal-500">Demo Accounts</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        <a href="#" onclick="document.getElementById('email').value='admin@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">admin@equipflow.com</a>
                        <a href="#" onclick="document.getElementById('email').value='sales@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">sales@equipflow.com</a>
                        <a href="#" onclick="document.getElementById('email').value='operations@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">operations@equipflow.com</a>
                        <a href="#" onclick="document.getElementById('email').value='finance@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">finance@equipflow.com</a>
                        <a href="#" onclick="document.getElementById('email').value='maintenance@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">maintenance@equipflow.com</a>
                        <a href="#" onclick="document.getElementById('email').value='customer@equipflow.com';document.getElementById('password').value='password';return false" class="rounded-sm border border-navy-700 px-3 py-2 text-charcoal-300 hover:border-brand-500">customer@equipflow.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
