<aside id="app-sidebar"
       class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy-950 text-charcoal-300 transition-transform duration-200 lg:translate-x-0">
    {{-- Brand --}}
    <div class="flex h-16 items-center justify-between border-b border-navy-800 px-5">
        <a href="{{ $isCustomer ? route('customer.dashboard') : route('admin.dashboard') }}" class="flex items-center gap-2.5">
            <span class="flex h-8 w-8 items-center justify-center bg-brand-500 font-display text-lg font-bold text-white">E</span>
            <span class="font-display text-xl font-bold uppercase tracking-wide text-white">Equip<span class="text-brand-500">Flow</span></span>
        </a>
        <button id="sidebar-close" type="button" class="text-charcoal-400 hover:text-white lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @foreach ($groups as $group)
            @if ($group['items'])
                <div>
                    <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-charcoal-500">{{ $group['label'] }}</p>
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            @php
                                $active = $item['active'] ?? false;
                            @endphp
                            <li>
                                <a href="{{ $item['url'] }}"
                                   class="group flex items-center gap-3 rounded-sm px-3 py-2 text-sm font-medium transition-colors {{ $active ? 'bg-brand-500 text-white' : 'text-charcoal-300 hover:bg-navy-900 hover:text-white' }}">
                                    <svg class="h-4.5 w-4.5 shrink-0" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">{!! $item['icon'] !!}</svg>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    @if (!empty($item['badge']))
                                        <span class="ml-auto rounded-sm bg-white/20 px-1.5 py-0.5 text-[10px] font-bold">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="border-t border-navy-800 p-4">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy-800 font-display text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-charcoal-500">{{ auth()->user()->roleLabel() }}</p>
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <a href="{{ route('landing') }}" class="btn-outline btn-sm flex-1 !border-navy-700 !text-charcoal-300 hover:!bg-navy-900 hover:!text-white">View Site</a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="btn-outline btn-sm w-full !border-navy-700 !text-charcoal-300 hover:!bg-navy-900 hover:!text-white">Logout</button>
            </form>
        </div>
    </div>
</aside>
