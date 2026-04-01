@extends('admin.layouts.app')

@section('title', 'Paket Layanan')
@section('page_title', 'Paket Spesial / Bundle')
@section('page_description', 'Kelola semua paket hemat yang berisi kombinasi beberapa layanan untuk pelanggan.')

@section('content')
<div class="space-y-6">
    <!-- Header Info -->
    <div class="glass-panel p-6 rounded-2xl border border-white/5 bg-linear-to-r from-primary/10 to-transparent">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="size-14 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/30">
                    <span class="material-symbols-outlined text-3xl text-primary">category</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-100">Daftar Operator Game</h2>
                    <p class="text-sm text-slate-400">Pilih operator untuk mengelola jenis produk dan layanan.</p>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('admin.packages.index') }}" method="GET" class="relative w-full md:w-64 group">
                    @if($type) <input type="hidden" name="type" value="{{ $type }}"> @endif
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Operator..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
                </form>
                <div class="flex bg-white/5 p-1 rounded-xl border border-white/10 w-full md:w-auto">
                    <a href="{{ route('admin.packages.index', ['search' => $search]) }}" class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all text-center {{ !$type ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-400 hover:text-slate-200' }}">Semua</a>
                    <a href="{{ route('admin.packages.index', ['type' => 'Topup Game', 'search' => $search]) }}" class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all text-center {{ $type == 'Topup Game' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-400 hover:text-slate-200' }}">Topup</a>
                    <a href="{{ route('admin.packages.index', ['type' => 'Voucher Game', 'search' => $search]) }}" class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all text-center {{ $type == 'Voucher Game' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-slate-400 hover:text-slate-200' }}">Voucher</a>
                </div>
                <button onclick="window.location.reload()" class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all w-full md:w-auto justify-center">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    <span class="md:hidden">Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Operators Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($operators as $op)
        <div class="relative group">
            <!-- Action Buttons -->
            <div class="absolute top-2 right-2 z-10 flex flex-col gap-1">
                <!-- Status Toggle -->
                <button onclick="toggleOperator({{ $op->id }})" id="toggle-{{ $op->id }}" class="size-8 rounded-full {{ $op->status == 'Aktif' ? 'bg-green-500/20 text-green-500 border-green-500/30' : 'bg-red-500/20 text-red-500 border-red-500/30' }} border flex items-center justify-center hover:scale-110 transition-all shadow-lg backdrop-blur-sm" title="Toggle Status">
                    <span class="material-symbols-outlined text-lg">{{ $op->status == 'Aktif' ? 'toggle_on' : 'toggle_off' }}</span>
                </button>
                <!-- Edit Settings -->
                <a href="{{ route('admin.packages.operator.edit', $op->id) }}" class="size-8 rounded-full bg-primary/20 text-primary border border-primary/30 flex items-center justify-center hover:scale-110 transition-all shadow-lg backdrop-blur-sm" title="Edit Pengaturan Input">
                    <span class="material-symbols-outlined text-lg">settings</span>
                </a>
            </div>

            <a href="{{ route('admin.packages.operator', $op->id) }}" id="card-{{ $op->id }}" class="glass-panel p-4 rounded-2xl border border-white/5 hover:border-primary/50 hover:bg-primary/5 transition-all flex flex-col items-center text-center gap-3 h-full {{ $op->status == 'Aktif' ? '' : 'opacity-60 grayscale' }}">
                <div class="size-20 rounded-xl overflow-hidden border border-white/10 group-hover:border-primary/30 transition-all shadow-inner">
                    <img src="{{ $op->icon }}" alt="{{ $op->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-200 group-hover:text-primary transition-colors">{{ $op->name }}</div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">{{ $op->jenis_count }} Jenis Produk</div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-span-full glass-panel py-20 rounded-2xl border border-white/5 flex flex-col items-center justify-center text-slate-500">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-20">inventory_2</span>
            <p class="text-sm">Belum ada operator terdaftar.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($operators->hasPages())
    <div class="glass-panel p-4 rounded-2xl border border-white/5 flex items-center justify-between">
        <p class="text-[10px] text-slate-500 uppercase font-bold">
            Menampilkan {{ $operators->firstItem() }}-{{ $operators->lastItem() }} dari {{ $operators->total() }} Operator
        </p>
        <div class="flex items-center gap-2">
            {{ $operators->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
    @endif
</div>

<script>
function toggleOperator(id) {
    const btn = document.getElementById('toggle-' + id);
    const card = document.getElementById('card-' + id);
    const icon = btn.querySelector('.material-symbols-outlined');

    // Add loading effect
    btn.classList.add('animate-pulse');
    btn.disabled = true;

    fetch(`/admin/packages/operator/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.status === 'Aktif') {
                btn.className = 'absolute top-2 right-2 z-10 size-8 rounded-full bg-green-500/20 text-green-500 border-green-500/30 border flex items-center justify-center hover:scale-110 transition-all shadow-lg backdrop-blur-sm';
                icon.textContent = 'toggle_on';
                card.classList.remove('opacity-60', 'grayscale');
            } else {
                btn.className = 'absolute top-2 right-2 z-10 size-8 rounded-full bg-red-500/20 text-red-500 border-red-500/30 border flex items-center justify-center hover:scale-110 transition-all shadow-lg backdrop-blur-sm';
                icon.textContent = 'toggle_off';
                card.classList.add('opacity-60', 'grayscale');
            }

            // Show toast if available (assuming a global toast function or simple alert)
            if (window.showToast) {
                showToast(data.message, 'success');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memperbarui status.');
    })
    .finally(() => {
        btn.classList.remove('animate-pulse');
        btn.disabled = false;
    });
}
</script>
@endsection
