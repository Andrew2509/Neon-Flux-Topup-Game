@extends('admin.layouts.app')

@section('title', 'Flash Sale')
@section('page_title', 'Manajemen Flash Sale')
@section('page_description', 'Atur promo waktu terbatas untuk menarik lebih banyak pembeli.')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="glass-panel px-4 py-2 rounded-xl border border-white/5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">bolt</span>
                <span class="text-sm font-bold text-slate-300">{{ $flashSales->count() }} Promo Terdaftar</span>
            </div>
        </div>
        
        <button onclick="openModal('addFlashSaleModal')" class="bg-primary hover:bg-primary-light text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined">add</span>
            Tambah Flash Sale
        </button>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-right">Harga Normal</th>
                        <th class="px-6 py-4 text-right">Harga Flash Sale</th>
                        <th class="px-6 py-4 text-center">Periode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($flashSales as $sale)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($sale->service->category && $sale->service->category->thumbnail)
                                <img src="{{ $sale->service->category->thumbnail }}" class="size-10 rounded-lg object-cover border border-white/10" alt="">
                                @else
                                <div class="size-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-500">image</span>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-bold text-slate-200">{{ $sale->service->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $sale->service->category->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-xs text-slate-400">
                            Rp {{ number_format($sale->service->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold text-primary">Rp {{ number_format($sale->discount_price, 0, ',', '.') }}</div>
                            @php
                                $diff = $sale->service->price - $sale->discount_price;
                                $percent = $sale->service->price > 0 ? ($diff / $sale->service->price) * 100 : 0;
                            @endphp
                            <div class="text-[10px] text-green-400 font-bold">Hemat {{ number_format($percent, 0) }}%</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <span class="bg-white/5 px-2 py-0.5 rounded text-[10px] text-slate-400 border border-white/10">Mulai: {{ $sale->start_time->format('d M y H:i') }}</span>
                                <span class="bg-red-500/10 px-2 py-0.5 rounded text-[10px] text-red-400 border border-red-500/20">Akhir: {{ $sale->end_time->format('d M y H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.flash-sales.toggle', $sale->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex items-center group focus:outline-none">
                                    <div class="w-10 h-5 transition-colors rounded-full shadow-inner {{ $sale->status == 'Aktif' ? 'bg-primary' : 'bg-slate-700' }}"></div>
                                    <div class="absolute left-0 w-5 h-5 transition-transform bg-white rounded-full shadow-md {{ $sale->status == 'Aktif' ? 'translate-x-full' : 'translate-x-0' }}"></div>
                                    <span class="ml-3 text-[10px] font-bold uppercase tracking-wider {{ $sale->status == 'Aktif' ? 'text-primary' : 'text-slate-500' }}">
                                        {{ $sale->status }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editFlashSale({{ json_encode($sale) }})" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-secondary hover:border-secondary transition-all" title="Edit">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <form action="{{ route('admin.flash-sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500 opacity-30">
                                <span class="material-symbols-outlined text-6xl mb-2">bolt</span>
                                <p class="text-xs uppercase tracking-widest font-bold">Belum ada Flash Sale</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Flash Sale -->
<div id="addFlashSaleModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="glass-panel w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">bolt</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Tambah Flash Sale</h3>
                    <p class="text-xs text-slate-400">Buat promo harga coret baru</p>
                </div>
            </div>
            <button onclick="closeModal('addFlashSaleModal')" class="text-slate-500 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Produk (Layanan)</label>
                <select name="service_id" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                    <option value="">-- Cari Layanan --</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}">{{ $s->category->name ?? 'Game' }} - {{ $s->name }} (Rp {{ number_format($s->price) }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Harga Promo (Rp)</label>
                    <input type="number" name="discount_price" required placeholder="Contoh: 15000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok (-1 = unlimit)</label>
                    <input type="number" name="stock" value="-1" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Berakhir</label>
                    <input type="datetime-local" name="end_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModal('addFlashSaleModal')" class="flex-1 px-6 py-3 border border-white/10 rounded-xl text-slate-400 font-bold hover:bg-white/5 transition-all text-sm">Batal</button>
                <button type="submit" class="flex-2 px-8 py-3 bg-primary text-white rounded-xl font-bold hover:brightness-110 shadow-lg shadow-primary/20 transition-all text-sm">Simpan Promo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Flash Sale -->
<div id="editFlashSaleModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="glass-panel w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-secondary/20 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined">edit</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Edit Flash Sale</h3>
                    <p class="text-xs text-slate-400">Sesuaikan periode atau harga promo</p>
                </div>
            </div>
            <button onclick="closeModal('editFlashSaleModal')" class="text-slate-500 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="editFlashSaleForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ID Produk</label>
                <input type="text" id="edit_service_name" readonly class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-slate-500 outline-none transition-all opacity-70">
                <input type="hidden" name="service_id" id="edit_service_id">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Harga Promo (Rp)</label>
                    <input type="number" name="discount_price" id="edit_discount_price" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok</label>
                    <input type="number" name="stock" id="edit_stock" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" id="edit_start_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Berakhir</label>
                    <input type="datetime-local" name="end_time" id="edit_end_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                <select name="status" id="edit_status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModal('editFlashSaleModal')" class="flex-1 px-6 py-3 border border-white/10 rounded-xl text-slate-400 font-bold hover:bg-white/5 transition-all text-sm">Batal</button>
                <button type="submit" class="flex-2 px-8 py-3 bg-secondary text-white rounded-xl font-bold hover:brightness-110 shadow-lg shadow-secondary/20 transition-all text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editFlashSale(sale) {
    const form = document.getElementById('editFlashSaleForm');
    form.action = `/admin/flash-sales/${sale.id}`;
    
    document.getElementById('edit_service_id').value = sale.service_id;
    document.getElementById('edit_service_name').value = `${sale.service.name}`;
    document.getElementById('edit_discount_price').value = sale.discount_price;
    document.getElementById('edit_stock').value = sale.stock;
    document.getElementById('edit_status').value = sale.status;
    
    // Format dates for datetime-local (YYYY-MM-DDTHH:MM)
    const startDate = new Date(sale.start_time);
    const endDate = new Date(sale.end_time);
    
    document.getElementById('edit_start_time').value = formatDateForInput(startDate);
    document.getElementById('edit_end_time').value = formatDateForInput(endDate);
    
    openModal('editFlashSaleModal');
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}
</script>
@endpush
@endsection
