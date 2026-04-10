<aside class="w-20 lg:w-64 bg-corp-sidebar flex flex-col transition-all duration-300 shadow-xl z-20" data-purpose="sidebar-navigation">
    <!-- Sidebar Logo -->
    <div class="h-16 flex items-center px-6 border-b border-white/10 centered-on-collapse">
        @if($logo = get_image_url('site_logo'))
            <div class="w-10 h-10 flex items-center justify-center overflow-hidden shrink-0">
                <img src="{{ $logo }}" alt="{{ get_setting('site_name', 'PrincePay Gaming') }}" class="max-h-full max-w-full object-contain">
            </div>
        @else
            <div class="w-8 h-8 bg-corp-accent rounded-lg flex items-center justify-center shadow-lg shrink-0">
                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        @endif
        
        @php
            $siteName = get_setting('site_name', 'NEON CORE');
            $parts = explode(' ', $siteName, 2);
            $firstPart = $parts[0] ?? '';
            $secondPart = $parts[1] ?? '';
        @endphp
        
        <span class="ml-3 font-bold text-lg tracking-tight text-white uppercase sidebar-logo-full hidden-on-collapse">
            {{ $firstPart }} <span class="text-corp-accent">{{ $secondPart }}</span>
        </span>
    </div>
    
    <!-- Navigation Links -->
    <nav class="flex-1 mt-6 px-4 space-y-1 overflow-y-auto no-scrollbar">
        @php
            $navItems = [
                ['url' => url('/'), 'icon' => 'home', 'label' => 'Halaman Top-up', 'svg' => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
                ['route' => 'user.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'svg' => '<path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
                ['route' => 'user.deposit', 'icon' => 'payment', 'label' => 'Isi Saldo', 'svg' => '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
                ['route' => 'user.riwayat', 'icon' => 'receipt', 'label' => 'Riwayat Pesanan', 'svg' => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
                ['route' => 'user.deposit.history', 'icon' => 'history', 'label' => 'Riwayat Deposit', 'svg' => '<path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
                ['route' => 'user.profile', 'icon' => 'person', 'label' => 'Profil Saya', 'svg' => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>'],
            ];
        @endphp

        @foreach($navItems as $item)
        @php
            $isActive = isset($item['route']) ? request()->routeIs($item['route']) : request()->is(ltrim(parse_url($item['url'], PHP_URL_PATH), '/') ?: '/');
            $href = isset($item['route']) ? route($item['route']) : $item['url'];
        @endphp
        <a href="{{ $href }}" 
           class="flex items-center px-3 py-2.5 rounded-lg group transition-all centered-on-collapse {{ $isActive ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}"
           title="{{ $item['label'] }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                {!! $item['svg'] !!}
            </svg>
            <span class="ml-3 font-medium text-sm hidden-on-collapse">{{ $item['label'] }}</span>
        </a>
        @endforeach
    </nav>

    <!-- Sidebar Footer (User Info) -->
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center justify-between bg-white/5 rounded-xl p-3 centered-on-collapse">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-slate-600 flex items-center justify-center font-bold text-white text-xs border border-white/10 uppercase shrink-0">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="ml-3 overflow-hidden hidden-on-collapse">
                    <p class="text-xs font-semibold text-white truncate max-w-[100px]">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate uppercase">{{ Auth::user()->role ? Auth::user()->role->name : 'Member' }} Account</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form-sidebar" class="hidden">@csrf</form>
            <button onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" 
                    class="text-slate-400 hover:text-white transition-colors hidden-on-collapse">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </button>
        </div>
    </div>
</aside>
