@extends('admin.layouts.app')

@section('title', 'Kategori TokoVoucher')
@section('page_title', 'Kategori TokoVoucher')
@section('page_description', 'Daftar kategori produk yang tersedia langsung dari API TokoVoucher.')

@section('content')
<div class="space-y-6">
    @if(isset($error))
    <div class="glass-panel p-4 rounded-2xl border-red-500/20 bg-red-500/10 flex gap-3 text-red-400">
        <span class="material-symbols-outlined">warning</span>
        <div class="text-sm font-medium">
            <p class="font-bold mb-1">Terjadi Masalah API</p>
            {{ $error }}
        </div>
    </div>
    @endif

    <div class="glass-panel rounded-3xl overflow-hidden border border-white/5 bg-white/5">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Daftar Kategori Produk</h3>
                    <p class="text-xs text-slate-400">Data real-time dari vendor TokoVoucher</p>
                </div>
            </div>
            <a href="{{ route('admin.tokovoucher.categories') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-all text-xs font-bold flex items-center gap-2 border border-white/10">
                <span class="material-symbols-outlined text-sm">refresh</span>
                Refresh Data
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/20 text-slate-400 uppercase text-[10px] tracking-widest font-bold">
                        <th class="px-6 py-4">ID API</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($categories as $cat)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 text-slate-400 font-mono text-xs">
                            #{{ $cat->ext_id }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-200">
                            {{ $cat->name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <label class="relative inline-flex items-center cursor-pointer group/toggle">
                                    <input type="checkbox" value="" class="sr-only peer" {{ $cat->status == 'Aktif' ? 'checked' : '' }} 
                                           onchange="toggleStatus({{ $cat->id }}, this)">
                                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-slate-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary/20 peer-checked:after:bg-primary peer-checked:after:border-primary/50"></div>
                                    <span class="ms-3 text-[10px] font-bold uppercase tracking-wider {{ $cat->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }} transition-colors status-label-{{ $cat->id }}">
                                        {{ $cat->status }}
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                            <button onclick="syncCategory({{ $cat->ext_id }}, this)" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all text-[10px] font-bold uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">sync</span>
                                Sinkronkan
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center opacity-50 italic">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl">inventory_2</span>
                                <span>Tidak ada kategori. Klik "Refresh Data" untuk mengambil data.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="sync-loading" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="glass-panel p-8 rounded-3xl border border-white/10 flex flex-col items-center gap-4 max-w-sm text-center">
            <div class="size-16 rounded-full border-4 border-primary/20 border-t-primary animate-spin"></div>
            <div>
                <h4 class="font-bold text-xl text-white">Memproses…</h4>
                <p class="text-sm text-slate-400 mt-1">Mengirim permintaan sinkron. Bila sudah sukses, tunggu 1–5 menit lalu muat ulang halaman.</p>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="glass-panel p-6 rounded-3xl border border-primary/20 bg-primary/5">
        <div class="flex gap-4">
            <div class="size-12 rounded-2xl bg-primary/20 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined text-2xl">info</span>
            </div>
            <div>
                <h4 class="font-bold text-slate-200 mb-1">Tentang Sinkronisasi Kategori</h4>
                <p class="text-sm text-slate-400 leading-relaxed">
                    - <strong>Status (On/Off)</strong>: Jika dinonaktifkan, semua operator dan produk di bawah kategori ini tidak akan muncul di halaman depan.<br>
                    - <strong>Sinkronkan</strong>: Menarik data Operator Game, Jenis Produk, hingga Layanan di bawah kategori tersebut.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function readJsonOrExplain(response) {
        const text = await response.text();
        if (!text || !text.trim()) {
            throw new Error('Respons kosong dari server (HTTP ' + response.status + '). Muat ulang halaman.');
        }
        let data = null;
        try {
            data = JSON.parse(text);
        } catch (e) {
            const snippet = text.trim().slice(0, 120).replace(/\s+/g, ' ');
            const hint = response.status === 419
                ? 'Sesi/halaman kadaluarsa (CSRF). Muat ulang halaman lalu coba lagi.'
                : (response.status === 401 || response.status === 403)
                    ? 'Akses ditolak. Pastikan Anda masih login sebagai admin.'
                    : (response.status === 504)
                        ? 'Gateway time-out: proses terlalu lama di server. Setelah update terbaru, sinkron TokoVoucher jalan di background — pastikan queue worker aktif dan coba Sinkronkan lagi.'
                        : 'Server mengembalikan halaman HTML, bukan JSON (kode ' + response.status + ').';
            throw new Error(hint + (snippet ? ' Cuplikan: ' + snippet : ''));
        }
        return data;
    }

    async function toggleStatus(id, checkbox) {
        const label = document.querySelector(`.status-label-${id}`);
        const originalChecked = checkbox.checked;
        
        try {
            const response = await fetch(`{{ url('admin/tokovoucher/categories') }}/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await readJsonOrExplain(response);
            
            if (result.status === 1) {
                label.textContent = result.new_status;
                if (result.new_status === 'Aktif') {
                    label.classList.remove('text-slate-500');
                    label.classList.add('text-primary');
                } else {
                    label.classList.remove('text-primary');
                    label.classList.add('text-slate-500');
                }
            } else {
                alert('Gagal: ' + (result.error_msg || result.message));
                checkbox.checked = !originalChecked;
            }
        } catch (err) {
            alert('Kesalahan jaringan: ' + err.message);
            checkbox.checked = !originalChecked;
        }
    }

    async function syncCategory(id, btn) {
        if(!confirm('Anda yakin ingin mensinkronkan semua produk untuk kategori ini?')) return;
        
        const loader = document.getElementById('sync-loading');
        loader.classList.remove('hidden');

        try {
            const response = await fetch(`{{ url('admin/tokovoucher/categories') }}/${id}/sync`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await readJsonOrExplain(response);
            
            if (result.status === 1) {
                alert(result.message);
                if (!result.async) {
                    location.reload();
                }
            } else {
                alert('Gagal: ' + (result.error_msg || result.message || 'Tidak diketahui'));
            }
        } catch (err) {
            alert('Kesalahan jaringan: ' + err.message);
        } finally {
            loader.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
