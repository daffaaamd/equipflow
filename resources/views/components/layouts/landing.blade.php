<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="EquipFlow — Heavy equipment rental and fleet management. Excavators, dozers, cranes, haul trucks and more for construction, mining, and infrastructure projects.">
    <title>{{ $title ?? 'EquipFlow' }} — Heavy Equipment Rental & Fleet Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-charcoal-50">

    {{-- Top bar: contact info + credit --}}
    <div class="bg-navy-950 text-white">
        <div class="container-equip flex items-center justify-between gap-4 py-2 text-[11px] uppercase tracking-widest">
            <div class="flex items-center gap-5 text-charcoal-300">
                <span class="flex items-center gap-2 sm:hidden">
                    <svg class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    Jakarta, Indonesia
                </span>
                <a href="tel:+622150501800" class="hidden items-center gap-2 transition-colors hover:text-white sm:flex">
                    <svg class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                    +62 21 5050 1800
                </a>
                <a href="mailto:info@equipflow.id" class="hidden items-center gap-2 transition-colors hover:text-white md:flex">
                    <svg class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                    info@equipflow.id
                </a>
            </div>
            <span class="flex items-center gap-2 text-charcoal-300">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                Buatan Daffa Ahmad Baihaqi
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <header x-data="{ scrolled: false, open: false }" x-on:scroll.window.passive="scrolled = window.scrollY > 24"
            class="sticky top-0 z-40 w-full border-b transition-all duration-300"
            :class="scrolled ? 'border-charcoal-200 bg-white/95 shadow-sm backdrop-blur' : 'border-transparent bg-white/90 backdrop-blur'">
        <nav class="container-equip flex h-16 items-center justify-between gap-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                <span class="font-display text-2xl font-bold uppercase tracking-wide text-navy-900">Equip<span class="text-brand-500">Flow</span></span>
            </a>

            <div class="hidden items-center gap-1 lg:flex">
                @php
                    $nav = [
                        'landing' => 'Home',
                        'catalog' => 'Equipment',
                        'solutions' => 'Solutions',
                        'services' => 'Services',
                        'projects' => 'Projects',
                        'about' => 'About',
                        'contact' => 'Contact',
                    ];
                @endphp
                @foreach ($nav as $routeName => $label)
                    <a href="{{ route($routeName) }}"
                       class="rounded-sm px-3 py-2 text-sm font-semibold uppercase tracking-wide transition-colors {{ request()->routeIs($routeName) ? 'text-brand-500' : 'text-charcoal-700 hover:text-navy-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-outline-navy btn-md">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-navy btn-md">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="btn-outline-navy btn-md">Sign In</a>
                    <a href="{{ route('quote.create') }}" class="btn-brand btn-md">Request a Quote</a>
                @endguest
            </div>

            <button id="menu-open" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-sm text-navy-900 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <button id="menu-close" type="button" class="hidden h-10 w-10 items-center justify-center rounded-sm text-navy-900 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </nav>

        <div id="mobile-menu" class="hidden border-t border-charcoal-200 bg-white lg:hidden">
            <div class="container-equip flex flex-col gap-1 py-4">
                @foreach ($nav as $routeName => $label)
                    <a href="{{ route($routeName) }}" class="rounded-sm px-3 py-2 text-sm font-semibold uppercase tracking-wide text-charcoal-700 hover:bg-charcoal-100">{{ $label }}</a>
                @endforeach
                <div class="mt-3 flex gap-3 border-t border-charcoal-200 pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-outline-navy btn-md flex-1">Dashboard</a>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="btn-outline-navy btn-md flex-1">Sign In</a>
                        <a href="{{ route('quote.create') }}" class="btn-brand btn-md flex-1">Request a Quote</a>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-navy-950 text-charcoal-300">
        <div class="container-equip grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center bg-brand-500 font-display text-xl font-bold text-white">E</span>
                    <span class="font-display text-2xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
                </a>
                <p class="mt-4 text-sm leading-relaxed">
                    Heavy equipment rental and fleet management for construction, mining, infrastructure, and industrial operations across Indonesia.
                </p>
                <p class="mt-4 flex items-center gap-2 text-[11px] uppercase tracking-widest text-charcoal-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    Buatan Daffa Ahmad Baihaqi
                </p>
            </div>
            <div>
                <h4 class="font-display text-lg font-semibold uppercase tracking-wide text-white">Company</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                    <li><a href="{{ route('solutions') }}" class="hover:text-white">Solutions</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Services</a></li>
                    <li><a href="{{ route('projects') }}" class="hover:text-white">Projects</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-lg font-semibold uppercase tracking-wide text-white">Fleet</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach ($footerCategories ?? \App\Models\EquipmentCategory::orderBy('sort_order')->take(5)->get() as $cat)
                        <li><a href="{{ route('catalog', ['category' => $cat->id]) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('catalog') }}" class="font-semibold text-brand-400 hover:text-brand-300">View All Equipment →</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-lg font-semibold uppercase tracking-wide text-white">Head Office</h4>
                <address class="mt-4 space-y-2 text-sm not-italic">
                    <p>Menara EquipFlow Lt. 18<br>Jl. Jend. Sudirman Kav. 52-53<br>Jakarta Selatan 12190, Indonesia</p>
                    <p class="pt-2"><a href="tel:+622150501800" class="hover:text-white">+62 21 5050 1800</a></p>
                    <p><a href="mailto:info@equipflow.id" class="hover:text-white">info@equipflow.id</a></p>
                </address>
                <a href="{{ route('quote.create') }}" class="btn-brand btn-md mt-5">Request a Quote</a>
            </div>
        </div>
        <div class="border-t border-navy-800">
            <div class="container-equip flex flex-col items-center justify-between gap-3 py-5 text-xs text-charcoal-400 sm:flex-row">
                <p>© {{ date('Y') }} EquipFlow. All rights reserved.</p>
                <p>Buatan Daffa Ahmad Baihaqi</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
