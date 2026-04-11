@extends('admin.layouts.app')

@section('title', 'Layanan')
@section('page_title', 'Daftar Layanan')
@section('page_description', 'Kelola semua produk / layanan satuan yang tersedia.')

@section('content')
<div class="space-y-6">
    {{-- <!-- Category / Operator Links -->
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-bold text-slate-400 mr-2">Lihat Operator:</span>
        <a href="{{ route('admin.categories', ['type' => 'Topup Game']) }}"
           class="px-6 py-2 rounded-xl text-sm font-bold transition-all border bg-white/5 text-slate-300 border-white/10 hover:bg-primary/20 hover:text-primary hover:border-primary/50">
            Topup Game
        </a>
        <a href="{{ route('admin.categories', ['type' => 'Voucher Game']) }}"
           class="px-6 py-2 rounded-xl text-sm font-bold transition-all border bg-white/5 text-slate-300 border-white/10 hover:bg-secondary/20 hover:text-secondary hover:border-secondary/50">
            Voucher Game
        </a>
    </div> --}}

    <!-- Filters & Actions -->
    <form action="{{ route('admin.services') }}" method="GET" class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <input type="hidden" name="type" value="{{ request('type') }}">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari layanan atau kode..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <select name="category" onchange="this.form.submit()" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none appearance-none text-slate-300">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-primary/20 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold transition-all hover:bg-primary/40 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Cari & Filter
            </button>
        </div>
    </form>

    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <button type="button" onclick="startBatchSync()" class="bg-secondary/10 hover:bg-secondary/20 text-secondary border border-secondary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-lg">sync</span>
                Sync Data
            </button>
            <button class="bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah
            </button>
        </div>
    </div>

    <!-- Services Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Nama Layanan</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4 text-right">Harga Modal</th>
                        <th class="px-6 py-4 text-right">Harga Jual</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 whitespace-nowrap">
                    @forelse($services as $service)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-200">{{ $service->name }}</div>
                            <div class="text-[10px] text-slate-500 font-mono">ID: {{ $service->id }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $service->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-xs">
                            <span class="px-2 py-1 bg-white/5 rounded-md border border-white/10">{{ $service->provider }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-slate-400">Rp {{ number_format($service->cost, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-right text-accent-blue">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.services.toggle', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center group focus:outline-none">
                                    <div class="w-10 h-5 transition-colors rounded-full shadow-inner {{ $service->status == 'Aktif' ? 'bg-primary' : 'bg-slate-700' }}"></div>
                                    <div class="absolute left-0 w-5 h-5 transition-transform bg-white rounded-full shadow-md {{ $service->status == 'Aktif' ? 'translate-x-full' : 'translate-x-0' }}"></div>
                                    <span class="ml-3 text-[10px] font-bold uppercase tracking-wider {{ $service->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }}">
                                        {{ $service->status }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-secondary hover:border-secondary transition-all" title="Edit">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <button class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-5xl mb-2 opacity-20">inventory_2</span>
                                <p class="text-sm">Belum ada layanan yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
        <div class="p-4 border-t border-white/5 flex items-center justify-between">
            <p class="text-[10px] text-slate-500 uppercase font-bold">Menampilkan {{ $services->firstItem() }} - {{ $services->lastItem() }} dari {{ $services->total() }} Layanan</p>
            <div class="flex items-center gap-2">
                {{ $services->links('vendor.pagination.tailwind-admin') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endpush

@push('scripts')
<!-- Sync Progress Modal -->
<div id="syncModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div class="glass-panel w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="p-6 border-b border-white/5 flex gap-4 items-center">
            <div class="size-12 rounded-2xl bg-secondary/20 flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-3xl animate-spin">sync</span>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">Sinkronisasi Produk</h3>
                <p class="text-xs text-slate-400">Sedang memperbarui layanan dari TokoVoucher...</p>
            </div>
        </div>
        <div class="p-8 space-y-6">
            <div class="space-y-2">
                <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider">
                    <span id="syncStatus" class="text-primary">Menyiapkan...</span>
                    <span id="syncPercent" class="text-slate-500">0%</span>
                </div>
                <div class="h-3 w-full bg-white/5 rounded-full overflow-hidden border border-white/5 p-0.5">
                    <div id="syncProgressBar" class="h-full bg-gradient-to-r from-primary to-secondary w-0 transition-all duration-500 rounded-full shadow-[0_0_15px_rgba(0,240,255,0.3)]"></div>
                </div>
            </div>
            <div id="syncDetail" class="bg-black/40 rounded-xl p-4 h-48 overflow-y-auto text-[10px] font-mono text-slate-400 space-y-1 scrollbar-thin">
                <div class="text-primary-light">>> Inisialisasi sinkronisasi batch...</div>
            </div>
        </div>
        <div class="p-4 bg-white/5 border-t border-white/5 flex justify-end">
            <button id="closeSyncBtn" onclick="location.reload()" class="hidden px-8 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:brightness-110 shadow-lg shadow-primary/20 transition-all">Selesai & Refresh</button>
        </div>
    </div>
</div>

<script>
async function startBatchSync() {
    const modal = document.getElementById('syncModal');
    const progressBar = document.getElementById('syncProgressBar');
    const statusText = document.getElementById('syncStatus');
    const percentText = document.getElementById('syncPercent');
    const detailBox = document.getElementById('syncDetail');
    const closeBtn = document.getElementById('closeSyncBtn');

    modal.classList.remove('hidden');

    const addLog = (msg, type = 'info') => {
        const div = document.createElement('div');
        div.className = 'py-0.5 border-b border-white/5 last:border-0';
        const time = new Date().toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });

        let colorClass = 'text-slate-400';
        if (type === 'error') colorClass = 'text-red-400 font-bold';
        if (type === 'success') colorClass = 'text-green-400';
        if (type === 'warn') colorClass = 'text-amber-400';

        div.innerHTML = `<span class="text-slate-600">[${time}]</span> <span class="${colorClass}">${msg}</span>`;
        detailBox.appendChild(div);
        detailBox.scrollTop = detailBox.scrollHeight;
    };

    try {
        addLog('Mengambil daftar operator dari Provider...');
        const listRes = await fetch('{{ route('admin.services.sync_list') }}');
        const listData = await listRes.json();

        if (listData.error) throw new Error(listData.error);

        const operators = listData.operators;
        addLog(`Ditemukan ${operators.length} operator aktif.`, 'success');

        const total = operators.length + 1; // +1 for cleanup
        let current = 0;

        for (const op of operators) {
            current++;
            const percent = Math.round((current / total) * 100);

            statusText.innerText = `Sync: ${op.name}`;
            percentText.innerText = `${percent}%`;
            progressBar.style.width = `${percent}%`;
            addLog(`Memproses "${op.name}"...`);

            try {
                const syncRes = await fetch('{{ route('admin.services.sync') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ operator_id: op.id })
                });

                const contentType = syncRes.headers.get("content-type");
                if (!syncRes.ok) {
                    let errMsg = 'HTTP Error ' + syncRes.status;
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        const errorData = await syncRes.json();
                        errMsg = errorData.error || errorData.message || errMsg;
                    } else {
                        const textErr = await syncRes.text();
                        console.error('Server non-JSON error:', textErr);
                        errMsg = 'Server Error (Check Console)';
                    }
                    throw new Error(errMsg);
                }

                const syncData = await syncRes.json();
                addLog(`Berhasil: ${op.name}`, 'success');
            } catch (err) {
                console.error(`Sync error for ${op.name}:`, err);
                const message = err.message || 'Unknown Error';
                addLog(`Gagal: ${op.name} -> ${message}`, 'error');
            }
        }

        // Final Cleanup
        current++;
        statusText.innerText = 'Finishing Up...';
        percentText.innerText = '100%';
        progressBar.style.width = '100%';
        addLog('Menjalankan pembersihan produk lama (Cleanup)...', 'warn');

        await fetch('{{ route('admin.services.sync') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cleanup_only: true })
        });

        addLog('SINKRONISASI SELESAI SEMPURNA!', 'success');
        statusText.innerText = 'Selesai!';
        statusText.className = 'text-green-400 animate-pulse';
        closeBtn.classList.remove('hidden');

    } catch (err) {
        addLog(`KESALAHAN FATAL: ${err.message}`, 'error');
        statusText.innerText = 'Gagal!';
        statusText.className = 'text-red-400';
        closeBtn.classList.remove('hidden');
        closeBtn.innerText = 'Tutup (Refresh Halaman)';
    }
}
</script>
@endpush
