@extends('admin.layouts.app')

@section('title', 'Kategori Game & Layanan')
@section('page_title', 'Daftar Kategori')
@section('page_description', 'Kelola semua kategori game dan layanan yang tersedia di sistem.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <!-- Type Filters -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.categories', array_merge(request()->query(), ['type' => 'Topup Game', 'page' => 1])) }}"
           class="px-6 py-2 rounded-xl text-sm font-bold transition-all border {{ request('type') == 'Topup Game' ? 'bg-primary text-white border-primary shadow-[0_0_15px_rgba(var(--primary-rgb),0.3)]' : 'bg-white/5 text-slate-400 border-white/10 hover:bg-white/10' }}">
            Topup Game
        </a>
        <a href="{{ route('admin.categories', array_merge(request()->query(), ['type' => 'Voucher Game', 'page' => 1])) }}"
           class="px-6 py-2 rounded-xl text-sm font-bold transition-all border {{ request('type') == 'Voucher Game' ? 'bg-secondary text-white border-secondary shadow-[0_0_15px_rgba(var(--secondary-rgb),0.3)]' : 'bg-white/5 text-slate-400 border-white/10 hover:bg-white/10' }}">
            Voucher Game
        </a>
        @if(request('type'))
        <a href="{{ route('admin.categories', request()->except(['type', 'page'])) }}" class="text-xs text-slate-500 hover:text-red-400 underline transition-colors">
            Reset Filter
        </a>
        @endif
    </div>

    <!-- Filters & Actions -->
    <form action="{{ route('admin.categories') }}" method="GET" class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <input type="hidden" name="type" value="{{ request('type') }}">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <button type="submit" class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all">
                <span class="material-symbols-outlined text-lg">search</span>
                Cari
            </button>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button type="button" class="bg-accent-blue/10 hover:bg-accent-blue/20 text-accent-blue border border-accent-blue/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Kategori
            </button>
        </div>
    </form>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($categories as $category)
        <div class="glass-panel p-5 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-primary/30 transition-all duration-300">
            <!-- Decorative Glow -->
            <div class="absolute -right-10 -top-10 size-32 bg-primary/10 blur-3xl rounded-full group-hover:bg-primary/20 transition-all"></div>

            <div class="flex items-start justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-xl overflow-hidden bg-slate-800 border border-white/10 flex items-center justify-center p-2 shadow-lg">
                        <img src="{{ $category->icon }}" alt="{{ $category->name }}" class="w-full h-full object-contain drop-shadow-lg" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=1e293b&color=fff';">
                    </div>
                </div>
                <!-- Action Menu -->
                <div class="relative dropdown-container">
                    <button class="text-slate-400 hover:text-white transition-colors p-1" onclick="toggleDropdown('dropdown-{{ $category->id }}')">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                    <!-- Dropdown Content (Hidden by default, you can implement JS to toggle) -->
                    <div id="dropdown-{{ $category->id }}" class="hidden absolute right-0 top-8 w-36 glass-panel border border-white/10 rounded-xl py-2 z-20 shadow-xl backdrop-blur-xl bg-slate-900/90">
                        <a href="{{ route('admin.services', ['category' => $category->id, 'type' => $category->type]) }}" class="px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">visibility</span> Lihat Produk</a>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">edit</span> Edit</a>
                        <a href="#" class="px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">delete</span> Hapus</a>
                    </div>
                </div>
            </div>

            <div class="mt-4 relative z-10">
                <h3 class="font-bold text-lg text-slate-100 group-hover:text-primary transition-colors line-clamp-1">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-mono line-clamp-1">/{{ $category->slug }}</p>
                @if($category->type)
                <p class="text-[10px] text-slate-400 mt-1">{{ $category->type }}</p>
                @endif
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-white/5 pt-4 relative z-10">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <span class="material-symbols-outlined text-lg text-accent-blue">inventory_2</span>
                    <span class="font-bold">{{ $category->services()->count() ?? 0 }}</span> <span class="text-xs">Produk</span>
                </div>
                <div>
                    @if($category->status === 'Aktif')
                        <span class="px-3 py-1 bg-green-500/10 text-green-400 text-[10px] font-bold rounded-full border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]">Aktif</span>
                    @elseif($category->status === 'Nonaktif')
                        <span class="px-3 py-1 bg-red-500/10 text-red-400 text-[10px] font-bold rounded-full border border-red-500/20">Nonaktif</span>
                    @else
                        <span class="px-3 py-1 bg-slate-500/10 text-slate-400 text-[10px] font-bold rounded-full border border-slate-500/20">Draft</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 flex flex-col items-center justify-center text-slate-500">
            <span class="material-symbols-outlined text-5xl mb-2 opacity-20">category</span>
            <p>Kategori tidak ditemukan.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="p-4 flex items-center justify-between glass-panel rounded-2xl">
        <p class="text-[10px] text-slate-500 uppercase font-bold">Menampilkan {{ $categories->firstItem() }} - {{ $categories->lastItem() }} dari {{ $categories->total() }} Kategori</p>
        <div class="flex items-center gap-2">
            {{ $categories->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function toggleDropdown(id) {
        // Simple toggle logic for the demo
        document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
            if (el.id !== id) el.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-container')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });
</script>
@endpush
@endsection
