@extends('admin.layouts.app')

@section('title', 'Pesanan')
@section('page_title', 'Daftar Pesanan')
@section('page_description', 'Kelola semua transaksi pesanan pelanggan dari satu tempat.')

@section('content')
<div class="space-y-6">
    <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
        Pesanan <strong class="text-slate-400">success</strong> biasanya sudah otomatis memanggil TokoVoucher lewat antrian.
        Tombol <strong class="text-emerald-400/90">Kirim ke game</strong> hanya tampil jika status <strong class="text-cyan-400/90">paid</strong> (pembayaran sudah masuk, belum terkirim ke supplier).
    </p>
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <form action="{{ route('admin.orders') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID, Nama, Produk..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            
            <div class="relative min-w-[140px]">
                <select name="status" onchange="this.form.submit()" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none appearance-none cursor-pointer">
                    <option value="all" {{ request('status') == 'all' || !request()->has('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu bayar</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid (siap kirim game)</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="failed_provider" {{ request('status') == 'failed_provider' ? 'selected' : '' }}>Failed provider</option>
                    <option value="failed_permanent" {{ request('status') == 'failed_permanent' ? 'selected' : '' }}>Failed permanen</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg pointer-events-none">expand_more</span>
            </div>

            @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('admin.orders') }}" class="text-slate-500 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-lg">close</span>
                </a>
            @endif
        </form>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button type="button" onclick="confirmDeleteAll()" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-red-500/5">
                <span class="material-symbols-outlined text-lg">delete_sweep</span>
                Hapus Semua
            </button>
            <button type="button" onclick="submitMassDelete()" id="massDeleteBtn" disabled class="bg-orange-500/10 text-orange-400 opacity-50 border border-orange-500/20 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all cursor-not-allowed">
                <span class="material-symbols-outlined text-lg">delete_forever</span>
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <form id="massDeleteForm" action="{{ route('admin.orders.mass_destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                        <tr>
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" id="selectAll" class="rounded bg-white/5 border-white/10 text-primary focus:ring-primary">
                            </th>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($orders as $order)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="order-checkbox rounded bg-white/5 border-white/10 text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-accent-blue">{{ $order->order_id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium">{{ $order->user->name ?? 'Guest' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $order->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400 max-w-[200px] truncate">{{ $order->product_name }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-xs">{{ $order->payment_method }}</td>
                            <td class="px-6 py-4 text-sm font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $status_color = match($order->status) {
                                        'success' => 'emerald',
                                        'paid', 'processing' => 'cyan',
                                        'pending_payment', 'pending' => 'amber',
                                        'failed', 'failed_provider', 'failed_permanent' => 'red',
                                        default => 'slate'
                                    };
                                @endphp
                                <span class="px-3 py-1 bg-{{ $status_color }}-500/10 text-{{ $status_color }}-400 text-[10px] font-bold rounded-full border border-{{ $status_color }}-500/20">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    @if($order->status === 'paid')
                                    <button type="button"
                                        onclick="fulfillTokovoucher({{ $order->id }}, @js($order->order_id))"
                                        class="px-2.5 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold uppercase tracking-wide hover:bg-emerald-500/30 transition-all inline-flex items-center gap-1"
                                        title="Panggil API TokoVoucher (sama seperti antrian otomatis)">
                                        <span class="material-symbols-outlined text-sm">send</span>
                                        Kirim ke game
                                    </button>
                                    @endif
                                    <a href="{{ route('track.order', ['order_id' => $order->order_id]) }}" target="_blank" rel="noopener"
                                        class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all"
                                        title="Lacak di situs">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-slate-500">
                                    <span class="material-symbols-outlined text-5xl opacity-20">order_approve</span>
                                    <p class="text-sm font-medium">Belum ada pesanan masuk.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="p-4 border-t border-white/5 flex items-center justify-between">
                {{ $orders->links('vendor.pagination.tailwind-admin') }}
            </div>
        </div>
    </form>
</div>

<form id="deleteAllForm" action="{{ route('admin.orders.destroy_all') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const selectAll = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const massDeleteBtn = document.getElementById('massDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    function updateActionButtons() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        selectedCount.textContent = checkedCount;
        
        if (checkedCount > 0) {
            massDeleteBtn.disabled = false;
            massDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            massDeleteBtn.classList.add('bg-red-500/20', 'hover:bg-red-500/30', 'text-red-400', 'border-red-500/30', 'shadow-lg', 'shadow-red-500/5');
            massDeleteBtn.classList.remove('bg-orange-500/10', 'text-orange-400', 'border-orange-500/20');
        } else {
            massDeleteBtn.disabled = true;
            massDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            massDeleteBtn.classList.remove('bg-red-500/20', 'hover:bg-red-500/30', 'text-red-400', 'border-red-500/30', 'shadow-lg', 'shadow-red-500/5');
            massDeleteBtn.classList.add('bg-orange-500/10', 'text-orange-400', 'border-orange-500/20');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            orderCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateActionButtons();
        });
    }

    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                const allChecked = Array.from(orderCheckboxes).every(c => c.checked);
                selectAll.checked = allChecked;
            }
            updateActionButtons();
        });
    });

    function submitMassDelete() {
        Swal.fire({
            title: 'Hapus Pesanan Terpilih?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('massDeleteForm').submit();
            }
        });
    }

    async function fulfillTokovoucher(orderPk, orderId) {
        const ask = await Swal.fire({
            title: 'Kirim ke TokoVoucher?',
            html: 'Pesanan <strong>' + orderId + '</strong> akan diproses ke ID game (API transaksi).',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batal',
            background: '#0f172a',
            color: '#fff'
        });
        if (!ask.isConfirmed) return;

        try {
            const res = await fetch('{{ url('/admin/orders') }}/' + orderPk + '/fulfill-tokovoucher', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const raw = await res.text();
            let data = {};
            try { data = raw ? JSON.parse(raw) : {}; } catch (e) {
                throw new Error('Respons server bukan JSON (kode ' + res.status + ').');
            }
            if (!res.ok || !data.success) {
                throw new Error(data.message || ('HTTP ' + res.status));
            }
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                background: '#0f172a',
                color: '#fff'
            });
            location.reload();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: e.message || 'Terjadi kesalahan',
                background: '#0f172a',
                color: '#fff'
            });
        }
    }

    function confirmDeleteAll() {
        Swal.fire({
            title: 'Kosongkan Semua Pesanan?',
            text: "PERINGATAN: Ini akan menghapus SELURUH data pesanan di database!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            background: '#0f172a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAllForm').submit();
            }
        });
    }
</script>
@endpush
