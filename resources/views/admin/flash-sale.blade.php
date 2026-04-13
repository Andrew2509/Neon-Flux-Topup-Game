@extends('admin.layouts.app')

@section('title', 'Flash Sale')
@section('page_title', 'Manajemen Flash Sale')
@section('page_description', 'Atur promo waktu terbatas untuk menarik lebih banyak pembeli.')

@section('content')
<style>
    /* Darken dropdown options for better visibility in dark theme */
    select option {
        background-color: #0f172a !important; /* bg-slate-900 */
        color: white !important;
    }
    select:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="glass-panel px-4 py-2 rounded-xl border border-white/5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">bolt</span>
                <span class="text-sm font-bold text-slate-300">{{ $flashSales->count() }} Promo Terdaftar</span>
            </div>
            
            <div class="relative flex-1 md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                <input type="text" id="tableSearch" placeholder="Cari promo..." class="w-full bg-white/5 border border-white/10 rounded-xl pl-9 pr-4 py-2 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all">
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
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Kategori</label>
                <div class="relative mb-2">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                    <input type="text" placeholder="Cari kategori..." class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-4 py-1.5 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all mb-1" onkeyup="filterSelect(this, 'add_category_id')">
                </div>
                <select id="add_category_id" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Operator</label>
                <div class="relative mb-2">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                    <input type="text" placeholder="Cari operator..." class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-4 py-1.5 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all mb-1" onkeyup="filterSelect(this, 'add_operator_id')">
                </div>
                <select id="add_operator_id" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900" disabled>
                    <option value="">-- Pilih Operator --</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Layanan</label>
                <div class="relative mb-2">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                    <input type="text" placeholder="Cari layanan..." class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-4 py-1.5 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all mb-1" onkeyup="filterSelect(this, 'add_service_id')">
                </div>
                <select name="service_id" id="add_service_id" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900" disabled>
                    <option value="">-- Pilih Layanan --</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Promo (Rp)</label>
                        <button type="button" onclick="toggleCalculator()" class="text-[10px] text-primary hover:text-white flex items-center gap-1 font-bold transition-colors">
                            <span class="material-symbols-outlined text-sm">calculate</span>
                            Kalkulator Profit
                        </button>
                    </div>
                    <input type="number" name="discount_price" id="add_discount_price" required placeholder="Contoh: 15000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok (-1 = unlimit)</label>
                    <input type="number" name="stock" value="-1" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all">
                </div>
            </div>

            <!-- Calculator Panel (Hidden initially) -->
            <div id="profitCalculator" class="hidden glass-panel rounded-2xl border border-primary/20 bg-primary/5 overflow-hidden animate-in fade-in slide-in-from-top-2">
                <div class="p-4 border-b border-primary/10 flex justify-between items-center">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-widest">Kalkulator Untung/Rugi</h4>
                    <button type="button" onclick="toggleCalculator()" class="text-slate-500 hover:text-white"><span class="material-symbols-outlined text-sm">close</span></button>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Harga Modal (Rp)</label>
                            <input type="number" id="calc_cost" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Fee Gateway (%)</label>
                            <input type="number" id="calc_fee_percent" value="2.5" step="0.1" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation()">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Target Margin (%)</label>
                            <input type="number" id="calc_margin_target" value="5" step="1" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation()">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Fee Admin (Rp)</label>
                            <input type="number" id="calc_fee_flat" value="0" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation()">
                        </div>
                    </div>
                    
                    <div id="calc_result" class="p-3 rounded-xl bg-black/20 space-y-2">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-slate-400">STATUS:</span>
                            <span id="status_label" class="text-slate-500">BELUM ADA DATA</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[9px] text-slate-500 font-bold uppercase">Estimasi Bersih</p>
                                <p id="profit_value" class="text-lg font-bold text-white">Rp 0</p>
                            </div>
                            <button type="button" onclick="toggleCalculator()" class="px-3 py-1.5 bg-primary text-[10px] font-bold rounded-lg text-white hover:brightness-110">Tutup Kalkulator</button>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div id="profit_bar" class="h-full bg-slate-500 w-0 transition-all duration-500"></div>
                        </div>
                        <p id="rec_label" class="text-[9px] text-slate-400"></p>
                    </div>
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
                <select name="status" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900">
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
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Promo (Rp)</label>
                        <button type="button" onclick="toggleCalculator('edit')" class="text-[10px] text-secondary hover:text-white flex items-center gap-1 font-bold transition-colors">
                            <span class="material-symbols-outlined text-sm">calculate</span>
                            Kalkulator Profit
                        </button>
                    </div>
                    <input type="number" name="discount_price" id="edit_discount_price" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok</label>
                    <input type="number" name="stock" id="edit_stock" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all">
                </div>
            </div>

            <!-- Calculator Panel Edit (Hidden initially) -->
            <div id="profitCalculatorEdit" class="hidden glass-panel rounded-2xl border border-secondary/20 bg-secondary/5 overflow-hidden">
                <div class="p-4 border-b border-secondary/10 flex justify-between items-center">
                    <h4 class="text-xs font-bold text-secondary uppercase tracking-widest">Kalkulator Untung/Rugi</h4>
                    <button type="button" onclick="toggleCalculator('edit')" class="text-slate-500 hover:text-white"><span class="material-symbols-outlined text-sm">close</span></button>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Harga Modal (Rp)</label>
                            <input type="number" id="edit_calc_cost" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Fee Gateway (%)</label>
                            <input type="number" id="edit_calc_fee_percent" value="2.5" step="0.1" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation('edit')">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Target Margin (%)</label>
                            <input type="number" id="edit_calc_margin_target" value="5" step="1" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation('edit')">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-bold mb-1">Fee Admin (Rp)</label>
                            <input type="number" id="edit_calc_fee_flat" value="0" class="w-full bg-black/20 border border-white/5 rounded-lg px-3 py-2 text-xs text-white" oninput="runCalculation('edit')">
                        </div>
                    </div>
                    
                    <div id="edit_calc_result" class="p-3 rounded-xl bg-black/20 space-y-2">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-slate-400">STATUS:</span>
                            <span id="edit_status_label" class="text-slate-500">SIAP</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[9px] text-slate-500 font-bold uppercase">Estimasi Bersih</p>
                                <p id="edit_profit_value" class="text-lg font-bold text-white">Rp 0</p>
                            </div>
                            <button type="button" onclick="toggleCalculator('edit')" class="px-3 py-1.5 bg-secondary text-[10px] font-bold rounded-lg text-white hover:brightness-110">Tutup Kalkulator</button>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div id="edit_profit_bar" class="h-full bg-slate-500 w-0 transition-all duration-500"></div>
                        </div>
                        <p id="edit_rec_label" class="text-[9px] text-slate-400"></p>
                    </div>
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
                <select name="status" id="edit_status" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900">
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
let serviceData = [];
let currentSelectedService = null;

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
    
    // Set cost for edit calculator
    document.getElementById('edit_calc_cost').value = sale.service.cost;
    
    // Format dates for datetime-local (YYYY-MM-DDTHH:MM)
    const startDate = new Date(sale.start_time);
    const endDate = new Date(sale.end_time);
    
    document.getElementById('edit_start_time').value = formatDateForInput(startDate);
    document.getElementById('edit_end_time').value = formatDateForInput(endDate);
    
    // Reset Edit Calculator View
    document.getElementById('profitCalculatorEdit').classList.add('hidden');
    
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

// Dependent Dropdowns for Add Modal
document.getElementById('add_category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const operatorSelect = document.getElementById('add_operator_id');
    const serviceSelect = document.getElementById('add_service_id');
    
    operatorSelect.innerHTML = '<option value="">-- Pilih Operator --</option>';
    operatorSelect.disabled = true;
    serviceSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
    serviceSelect.disabled = true;
    
    if (categoryId) {
        fetch(`/admin/flash-sales/operators/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(op => {
                    const option = document.createElement('option');
                    option.value = op.id;
                    option.textContent = op.name;
                    operatorSelect.appendChild(option);
                });
                operatorSelect.disabled = false;
            });
    }
});

document.getElementById('add_operator_id').addEventListener('change', function() {
    const categoryId = this.value;
    const operatorSelect = document.getElementById('add_operator_id');
    const serviceSelect = document.getElementById('add_service_id');
    
    serviceSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
    serviceSelect.disabled = true;
    serviceData = []; // Reset cache
    
    if (categoryId) {
        fetch(`/admin/flash-sales/services/${this.value}`)
            .then(response => response.json())
            .then(data => {
                serviceData = data;
                data.forEach(s => {
                    const option = document.createElement('option');
                    option.value = s.id;
                    option.textContent = `${s.name} (Rp ${new Intl.NumberFormat('id-ID').format(s.price)})`;
                    serviceSelect.appendChild(option);
                });
                serviceSelect.disabled = false;
            });
    }
});

document.getElementById('add_service_id').addEventListener('change', function() {
    const serviceId = this.value;
    currentSelectedService = serviceData.find(s => s.id == serviceId);
    
    if (currentSelectedService) {
        document.getElementById('calc_cost').value = currentSelectedService.cost;
        runCalculation('add');
    }
});

document.getElementById('add_discount_price').addEventListener('input', () => runCalculation('add'));
document.getElementById('edit_discount_price').addEventListener('input', () => runCalculation('edit'));

function toggleCalculator(context = 'add') {
    const id = context === 'add' ? 'profitCalculator' : 'profitCalculatorEdit';
    const panel = document.getElementById(id);
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) runCalculation(context);
}

function runCalculation(context = 'add') {
    const isEdit = context === 'edit';
    const prefix = isEdit ? 'edit_' : '';
    const calcPrefix = isEdit ? 'edit_calc_' : 'calc_';
    
    const promoPrice = parseFloat(document.getElementById(prefix + 'discount_price').value) || 0;
    const cost = parseFloat(document.getElementById(calcPrefix + 'cost').value) || 0;
    const feePercent = parseFloat(document.getElementById(calcPrefix + 'fee_percent').value) || 0;
    const feeFlat = parseFloat(document.getElementById(calcPrefix + 'fee_flat').value) || 0;
    const marginTargetPct = parseFloat(document.getElementById(calcPrefix + 'margin_target').value) || 0;
    
    const profitLabel = document.getElementById(isEdit ? 'edit_profit_value' : 'profit_value');
    const statusLabel = document.getElementById(isEdit ? 'edit_status_label' : 'status_label');
    const profitBar = document.getElementById(isEdit ? 'edit_profit_bar' : 'profit_bar');
    const recLabel = document.getElementById(isEdit ? 'edit_rec_label' : 'rec_label');

    if (!cost || !promoPrice) {
        profitLabel.textContent = "Rp 0";
        statusLabel.textContent = "BELUM ADA DATA";
        statusLabel.className = "text-slate-500 font-bold";
        profitBar.style.width = "0%";
        return;
    }
    
    const gatewayFee = promoPrice * (feePercent / 100);
    const totalCost = cost + gatewayFee + feeFlat;
    const profit = promoPrice - totalCost;
    const marginPct = (profit / promoPrice) * 100;
    
    profitLabel.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(Math.round(profit))}`;
    
    // Recommendations
    const minSafePrice = Math.ceil((cost + feeFlat) / (1 - (feePercent / 100)));
    const targetSafePctValue = (feePercent + marginTargetPct) / 100;
    const targetSafePrice = targetSafePctValue < 1 ? Math.ceil((cost + feeFlat) / (1 - targetSafePctValue)) : 'N/A';
    
    if (profit <= 0) {
        statusLabel.textContent = '❌ RUGI';
        statusLabel.className = 'text-red-500 font-bold text-[10px]';
        profitBar.className = isEdit ? 'h-full bg-red-500' : 'h-full bg-red-500 transition-all duration-500';
        profitBar.style.width = '100%';
        recLabel.textContent = `Harga minimum agar tidak rugi: Rp ${new Intl.NumberFormat('id-ID').format(minSafePrice)}`;
    } else if (marginPct < marginTargetPct) {
        statusLabel.textContent = '⚠️ MARGIN RENDAH';
        statusLabel.className = 'text-yellow-500 font-bold text-[10px]';
        profitBar.className = isEdit ? 'h-full bg-yellow-500' : 'h-full bg-yellow-500 transition-all duration-500';
        profitBar.style.width = `${Math.min(100, (marginPct / marginTargetPct) * 100)}%`;
        recLabel.textContent = `Saran harga untuk margin ${marginTargetPct}%: Rp ${targetSafePrice === 'N/A' ? 'N/A' : new Intl.NumberFormat('id-ID').format(targetSafePrice)}`;
    } else {
        statusLabel.textContent = '✅ UNTUNG';
        statusLabel.className = 'text-green-500 font-bold text-[10px]';
        profitBar.className = isEdit ? 'h-full bg-green-500' : 'h-full bg-green-500 transition-all duration-500';
        profitBar.style.width = '100%';
        recLabel.textContent = 'Harga promo sudah sesuai target margin.';
    }
}

function filterSelect(input, selectId) {
    const filter = input.value.toLowerCase();
    const select = document.getElementById(selectId);
    const options = select.getElementsByTagName('option');
    
    for (let i = 0; i < options.length; i++) {
        const text = options[i].textContent.toLowerCase();
        // Always show the empty/placeholder option
        if (options[i].value === "") {
            options[i].style.display = "";
            continue;
        }
        
        if (text.indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
}

// Table Search Logic
document.getElementById('tableSearch').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>
@endpush
@endsection
