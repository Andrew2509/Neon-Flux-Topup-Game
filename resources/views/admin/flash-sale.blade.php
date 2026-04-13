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
        
        <div class="flex gap-2">
            <button onclick="openModal('generateFlashSaleModal')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-indigo-500/20">
                <span class="material-symbols-outlined">rocket_launch</span>
                Generate Otomatis
            </button>
            <button onclick="openModal('addFlashSaleModal')" class="bg-primary hover:bg-primary-light text-white px-6 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">add</span>
                Tambah Flash Sale
            </button>
        </div>
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
    <div class="glass-panel w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl border border-white/10 animate-in zoom-in duration-300">
        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">bolt</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wider">Tambah Flash Sale</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Konfigurasi promo harga coret baru</p>
                </div>
            </div>
            <button onclick="closeModal('addFlashSaleModal')" class="size-8 rounded-full flex items-center justify-center hover:bg-white/10 text-slate-500 hover:text-white transition-all">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column: Product Selection -->
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
                        1. Pilih Produk
                    </h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori</label>
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
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Operator</label>
                        <div class="relative mb-2">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                            <input type="text" placeholder="Cari operator..." class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-4 py-1.5 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all mb-1" onkeyup="filterSelect(this, 'add_operator_id')">
                        </div>
                        <select id="add_operator_id" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900" disabled>
                            <option value="">-- Pilih Operator --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Layanan</label>
                        <div class="relative mb-2">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                            <input type="text" placeholder="Cari layanan..." class="w-full bg-white/5 border border-white/10 rounded-lg pl-9 pr-4 py-1.5 text-xs text-white focus:ring-1 focus:ring-primary outline-none transition-all mb-1" onkeyup="filterSelect(this, 'add_service_id')">
                        </div>
                        <select name="service_id" id="add_service_id" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900" disabled>
                            <option value="">-- Pilih Layanan --</option>
                        </select>
                    </div>
                </div>

                <!-- Right Column: Promo Config -->
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-secondary uppercase tracking-widest flex items-center gap-2">
                        <span class="size-1.5 rounded-full bg-secondary animate-pulse"></span>
                        2. Detail Promo
                    </h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Promo</label>
                                <button type="button" onclick="toggleCalculator()" class="text-[9px] text-primary hover:text-white flex items-center gap-1 font-bold">
                                    <span class="material-symbols-outlined text-xs">calculate</span>
                                    Profit
                                </button>
                            </div>
                            <input type="number" name="discount_price" id="add_discount_price" required placeholder="15000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok (-1 unlimit)</label>
                            <input type="number" name="stock" value="-1" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all text-sm">
                        </div>
                    </div>

                    <!-- Calculator Panel (Now part of the layout) -->
                    <div id="profitCalculator" class="hidden glass-panel rounded-2xl border border-primary/20 bg-primary/5 overflow-hidden">
                        <div class="p-3 border-b border-primary/10 flex justify-between items-center">
                            <h4 class="text-[9px] font-bold text-primary uppercase tracking-widest">Kalkulator Untung</h4>
                            <button type="button" onclick="toggleCalculator()" class="text-slate-500 hover:text-white"><span class="material-symbols-outlined text-xs">close</span></button>
                        </div>
                        <div class="p-3 space-y-2">
                            <!-- Hidden inputs to keep JS working without cluttering compact UI -->
                            <input type="hidden" id="calc_margin_target" value="5">
                            <input type="hidden" id="calc_fee_flat" value="0">
                            
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold mb-1 uppercase">Modal</label>
                                    <input type="number" id="calc_cost" class="w-full bg-black/20 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white" readonly>
                                </div>
                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold mb-1 uppercase">Fee iPaymu (%)</label>
                                    <input type="number" id="calc_fee_percent" value="2.5" step="0.1" class="w-full bg-black/20 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white" oninput="runCalculation()">
                                </div>
                            </div>
                            <div id="calc_result" class="p-2 rounded-lg bg-black/40">
                                <div class="flex justify-between items-center mb-1">
                                    <span id="status_label" class="text-[9px] font-bold">BELUM ADA DATA</span>
                                    <span id="profit_value" class="text-xs font-bold text-white">Rp 0</span>
                                </div>
                                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div id="profit_bar" class="h-full bg-slate-500 w-0 transition-all duration-500"></div>
                                </div>
                                <p id="rec_label" class="text-[8px] text-slate-500 mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Mulai</label>
                                <input type="datetime-local" name="start_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Berakhir</label>
                                <input type="datetime-local" name="end_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status Promo</label>
                            <select name="status" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary outline-none transition-all [&>option]:bg-slate-900 text-sm">
                                <option value="Aktif">Aktif (Langsung tayang)</option>
                                <option value="Nonaktif">Nonaktif (Draft)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-white/5 flex gap-3">
                <button type="button" onclick="closeModal('addFlashSaleModal')" class="flex-1 px-6 py-3 border border-white/10 rounded-xl text-slate-400 font-bold hover:bg-white/5 transition-all text-sm">Batal</button>
                <button type="submit" class="flex-[2] px-8 py-3 bg-primary text-white rounded-xl font-bold hover:brightness-110 shadow-lg shadow-primary/20 transition-all text-sm uppercase tracking-widest">Simpan Promo Flash Sale</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Flash Sale -->
<div id="editFlashSaleModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="glass-panel w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl border border-white/10 animate-in zoom-in duration-300">
        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-secondary/20 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined">edit</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wider">Edit Flash Sale</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Perbarui pengaturan promo terpilih</p>
                </div>
            </div>
            <button onclick="closeModal('editFlashSaleModal')" class="size-8 rounded-full flex items-center justify-center hover:bg-white/10 text-slate-500 hover:text-white transition-all">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <form id="editFlashSaleForm" method="POST" class="p-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column: Product Info -->
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-secondary uppercase tracking-widest flex items-center gap-2">
                        <span class="size-1.5 rounded-full bg-secondary animate-pulse"></span>
                        1. Data Produk
                    </h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Produk / Layanan</label>
                        <input type="text" id="edit_service_name" readonly class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-slate-400 outline-none transition-all cursor-not-allowed">
                        <input type="hidden" name="service_id" id="edit_service_id">
                    </div>

                    <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Informasi Tambahan</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-400">Tipe Produk</span>
                            <span class="px-2 py-0.5 rounded-md bg-white/10 text-[10px] text-white font-bold">DIGITAL</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-400">Keamanan Transaksi</span>
                            <span class="flex items-center gap-1 text-[10px] text-green-500 font-bold">
                                <span class="material-symbols-outlined text-[12px]">verified</span> TERVERIFIKASI
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Promo Config -->
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
                        2. Penyesuaian Promo
                    </h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Promo</label>
                                <button type="button" onclick="toggleCalculator('edit')" class="text-[9px] text-secondary hover:text-white flex items-center gap-1 font-bold">
                                    <span class="material-symbols-outlined text-xs">calculate</span>
                                    Profit
                                </button>
                            </div>
                            <input type="number" name="discount_price" id="edit_discount_price" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stok</label>
                            <input type="number" name="stock" id="edit_stock" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all text-sm">
                        </div>
                    </div>

                    <!-- Calculator Panel Edit -->
                    <div id="profitCalculatorEdit" class="hidden glass-panel rounded-2xl border border-secondary/20 bg-secondary/5 overflow-hidden">
                        <div class="p-3 border-b border-secondary/10 flex justify-between items-center">
                            <h4 class="text-[9px] font-bold text-secondary uppercase tracking-widest">Kalkulator Untung</h4>
                            <button type="button" onclick="toggleCalculator('edit')" class="text-slate-500 hover:text-white"><span class="material-symbols-outlined text-xs">close</span></button>
                        </div>
                        <div class="p-3 space-y-2">
                            <input type="hidden" id="edit_calc_margin_target" value="5">
                            <input type="hidden" id="edit_calc_fee_flat" value="0">
                            
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold mb-1 uppercase">Modal</label>
                                    <input type="number" id="edit_calc_cost" class="w-full bg-black/20 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white" readonly>
                                </div>
                                <div>
                                    <label class="block text-[9px] text-slate-400 font-bold mb-1 uppercase">Fee (%)</label>
                                    <input type="number" id="edit_calc_fee_percent" value="2.5" step="0.1" class="w-full bg-black/20 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white" oninput="runCalculation('edit')">
                                </div>
                            </div>
                            <div id="edit_calc_result" class="p-2 rounded-lg bg-black/40">
                                <div class="flex justify-between items-center mb-1">
                                    <span id="edit_status_label" class="text-[9px] font-bold">READY</span>
                                    <span id="edit_profit_value" class="text-xs font-bold text-white">Rp 0</span>
                                </div>
                                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div id="edit_profit_bar" class="h-full bg-slate-500 w-0 transition-all duration-500"></div>
                                </div>
                                <p id="edit_rec_label" class="text-[8px] text-slate-500 mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Mulai</label>
                            <input type="datetime-local" name="start_time" id="edit_start_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Berakhir</label>
                            <input type="datetime-local" name="end_time" id="edit_end_time" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status Promo</label>
                        <select name="status" id="edit_status" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-secondary outline-none transition-all [&>option]:bg-slate-900 text-sm">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-white/5 flex gap-3">
                <button type="button" onclick="closeModal('editFlashSaleModal')" class="flex-1 px-6 py-3 border border-white/10 rounded-xl text-slate-400 font-bold hover:bg-white/5 transition-all text-sm">Batal</button>
                <button type="submit" class="flex-[2] px-8 py-3 bg-secondary text-white rounded-xl font-bold hover:brightness-110 shadow-lg shadow-secondary/20 transition-all text-sm uppercase tracking-widest">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Generate Flash Sale Otomatis -->
<div id="generateFlashSaleModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="glass-panel w-full max-w-5xl rounded-3xl overflow-hidden shadow-2xl border border-white/10 animate-in zoom-in duration-300 max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-indigo-600/20">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <span class="material-symbols-outlined">rocket_launch</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wider">Generate Flash Sale Otomatis</h3>
                    <p class="text-[10px] text-indigo-300 font-medium font-mono">Auto-pilot mode for bulk promotions</p>
                </div>
            </div>
            <button onclick="closeModal('generateFlashSaleModal')" class="size-8 rounded-full flex items-center justify-center hover:bg-white/10 text-slate-500 hover:text-white transition-all">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <!-- Config Form -->
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 p-6 rounded-2xl bg-white/5 border border-white/5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jumlah Produk</label>
                        <input type="number" id="gen_count" value="10" min="1" max="50" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Filter Kategori</label>
                        <select id="gen_category" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Target Margin (%)</label>
                        <input type="number" id="gen_margin" value="5" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="generateBatch()" id="btnGenerate" class="w-full h-[41px] bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20">
                            <span class="material-symbols-outlined text-sm">cached</span>
                            Generate Sekarang
                        </button>
                    </div>
                </div>

                <!-- Global Config (Timing & Fees) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Mulai</label>
                        <input type="datetime-local" id="gen_start" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Berakhir</label>
                        <input type="datetime-local" id="gen_end" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Fee API/Admin (Rp)</label>
                        <input type="number" id="gen_fee_flat" value="0" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-indigo-500 outline-none transition-all text-sm" oninput="recalcBatch()">
                    </div>
                </div>

                <!-- Preview Table Area -->
                <div id="gen_preview_container" class="hidden space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                            <span class="size-2 rounded-full bg-indigo-500 animate-ping"></span>
                            Pratinjau Batch Flash Sale
                        </h4>
                        <div class="flex items-center gap-2">
                            <label class="text-[10px] text-slate-500 font-bold uppercase cursor-pointer flex items-center gap-1.5">
                                <input type="checkbox" id="gen_prevent_loss" checked class="rounded bg-white/5 border-white/10 text-indigo-600 focus:ring-0">
                                Blokir Jika Rugi
                            </label>
                        </div>
                    </div>
                    
                    <div class="glass-panel overflow-hidden border border-white/5 rounded-2xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-white/5 text-[9px] uppercase tracking-wider text-slate-500 font-bold">
                                    <tr>
                                        <th class="px-4 py-3">Produk</th>
                                        <th class="px-4 py-3 text-right">Modal</th>
                                        <th class="px-4 py-3 text-right">Harga Normal</th>
                                        <th class="px-4 py-3 text-right w-40">Harga Promo</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="batch_preview_body" class="divide-y divide-white/5">
                                    <!-- Dynamic Content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-white/5 bg-white/5 flex gap-3">
            <button type="button" onclick="closeModal('generateFlashSaleModal')" class="flex-1 px-6 py-3 border border-white/10 rounded-xl text-slate-400 font-bold hover:bg-white/5 transition-all text-sm">Batal</button>
            <button type="button" onclick="saveBatch()" id="btnSaveBatch" disabled class="flex-[2] px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:brightness-110 shadow-lg shadow-indigo-600/20 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Simpan Batch Promo
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let serviceData = [];
let currentSelectedService = null;
let generatedBatchData = [];

// --- Batch Generator Logic ---
async function generateBatch() {
    const btn = document.getElementById('btnGenerate');
    const count = document.getElementById('gen_count').value;
    const category = document.getElementById('gen_category').value;
    const btnSave = document.getElementById('btnSaveBatch');
    
    if (!count || count < 1) return alert('Jumlah produk minimal 1');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">cached</span> Generating...';
    
    try {
        const response = await fetch(`/admin/flash-sales/random-products?count=${count}&category_id=${category}`);
        const data = await response.json();
        
        if (data.length === 0) {
            alert('Tidak ada produk tersedia untuk kriteria ini.');
            return;
        }
        
        generatedBatchData = data;
        renderBatchTable();
        document.getElementById('gen_preview_container').classList.remove('hidden');
        btnSave.disabled = false;
        
    } catch (error) {
        alert('Gagal mengambil data produk: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm">cached</span> Generate Sekarang';
    }
}

function renderBatchTable() {
    const body = document.getElementById('batch_preview_body');
    const margin = parseFloat(document.getElementById('gen_margin').value) || 0;
    const feeFlat = parseFloat(document.getElementById('gen_fee_flat').value) || 0;
    const feePct = 2.5; // Default iPaymu
    
    body.innerHTML = '';
    
    generatedBatchData.forEach((item, index) => {
        // Calculate recommended price: Promo = (Cost + FlatFee) / (1 - (FeePct + Margin)/100)
        const targetPctValue = (feePct + margin) / 100;
        let promoPrice = Math.ceil((item.cost + feeFlat) / (1 - targetPctValue));
        
        // Ensure it doesn't exceed normal price (keep min 5% discount if possible)
        if (promoPrice >= item.price) {
            promoPrice = Math.floor(item.price * 0.95);
        }
        
        item.calculated_promo = promoPrice;

        const row = document.createElement('tr');
        row.className = 'hover:bg-white/5 transition-colors group border-b border-white/5';
        row.innerHTML = `
            <td class="px-4 py-4">
                <div class="text-sm font-bold text-slate-200">${item.name}</div>
                <div class="text-[9px] text-slate-500 font-mono uppercase">${item.category.name}</div>
            </td>
            <td class="px-4 py-4 text-right text-xs text-slate-400 font-mono">
                Rp ${new Intl.NumberFormat('id-ID').format(item.cost)}
            </td>
            <td class="px-4 py-4 text-right text-xs text-slate-400 font-mono">
                Rp ${new Intl.NumberFormat('id-ID').format(item.price)}
            </td>
            <td class="px-4 py-4 text-right">
                <input type="number" value="${promoPrice}" 
                    class="w-full bg-black/40 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-indigo-400 font-bold focus:ring-1 focus:ring-indigo-500 outline-none"
                    oninput="updateBatchRow(${index}, this.value)">
            </td>
            <td class="px-4 py-4 text-center" id="batch_status_${index}">
                ${getBatchStatusHtml(item, promoPrice)}
            </td>
            <td class="px-4 py-4 text-center">
                <button onclick="removeBatchRow(${index})" class="text-red-500/50 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </td>
        `;
        body.appendChild(row);
    });
    
    validateBatchSave();
}

function getBatchStatusHtml(item, promoPrice) {
    const feePct = 2.5;
    const feeFlat = parseFloat(document.getElementById('gen_fee_flat').value) || 0;
    const totalCost = item.cost + (promoPrice * (feePct / 100)) + feeFlat;
    const profit = promoPrice - totalCost;
    
    if (profit <= 0) {
        return '<span class="px-2 py-0.5 rounded-md bg-red-500/10 text-red-500 text-[9px] font-bold border border-red-500/20">❌ RUGI</span>';
    }
    return '<span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-500 text-[9px] font-bold border border-green-500/20">✅ UNTUNG</span>';
}

function updateBatchRow(index, value) {
    const price = parseFloat(value) || 0;
    generatedBatchData[index].calculated_promo = price;
    document.getElementById(`batch_status_${index}`).innerHTML = getBatchStatusHtml(generatedBatchData[index], price);
    validateBatchSave();
}

function removeBatchRow(index) {
    generatedBatchData.splice(index, 1);
    renderBatchTable();
}

function recalcBatch() {
    renderBatchTable();
}

function validateBatchSave() {
    const btn = document.getElementById('btnSaveBatch');
    const preventLoss = document.getElementById('gen_prevent_loss').checked;
    
    if (generatedBatchData.length === 0) {
        btn.disabled = true;
        return;
    }
    
    if (preventLoss) {
        const hasLoss = generatedBatchData.some(item => {
            const feePct = 2.5;
            const feeFlat = parseFloat(document.getElementById('gen_fee_flat').value) || 0;
            const totalCost = item.cost + (item.calculated_promo * (feePct / 100)) + feeFlat;
            return (item.calculated_promo - totalCost) <= 0;
        });
        btn.disabled = hasLoss;
    } else {
        btn.disabled = false;
    }
}

async function saveBatch() {
    const btn = document.getElementById('btnSaveBatch');
    const startTime = document.getElementById('gen_start').value;
    const endTime = document.getElementById('gen_end').value;
    
    if (!startTime || !endTime) {
        alert('Harap isi waktu mulai dan berakhir untuk batch ini!');
        return;
    }
    
    if (!confirm(`Simpan ${generatedBatchData.length} promo Flash Sale sekaligus?`)) return;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">cached</span> Menyimpan...';
    
    const items = generatedBatchData.map(item => ({
        service_id: item.id,
        discount_price: item.calculated_promo,
        start_time: startTime,
        end_time: endTime,
        status: 'Aktif',
        stock: 100 // Default stock for bulk
    }));
    
    try {
        const response = await fetch('/admin/flash-sales/bulk-store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items })
        });
        
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Gagal menyimpan batch: ' + result.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan saat menyimpan: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">save</span> Simpan Batch Promo';
    }
}

// --- End Batch Generator Logic ---

function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Initialize defaults for Generator if opening
    if (id === 'generateFlashSaleModal') {
        const now = new Date();
        const tomorrow = new Date(now.getTime() + (24 * 60 * 60 * 1000));
        
        document.getElementById('gen_start').value = formatDateForInput(now);
        document.getElementById('gen_end').value = formatDateForInput(tomorrow);
    }
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
    const prefix = isEdit ? 'edit_' : 'add_';
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
        profitBar.className = 'h-full bg-red-500 transition-all duration-500';
        profitBar.style.width = '100%';
        recLabel.textContent = `Harga minimum agar tidak rugi: Rp ${new Intl.NumberFormat('id-ID').format(minSafePrice)}`;
    } else if (marginPct < marginTargetPct) {
        statusLabel.textContent = '⚠️ MARGIN RENDAH';
        statusLabel.className = 'text-yellow-500 font-bold text-[10px]';
        profitBar.className = 'h-full bg-yellow-500 transition-all duration-500';
        profitBar.style.width = `${Math.min(100, (marginPct / marginTargetPct) * 100)}%`;
        recLabel.textContent = `Saran harga untuk margin ${marginTargetPct}%: Rp ${targetSafePrice === 'N/A' ? 'N/A' : new Intl.NumberFormat('id-ID').format(targetSafePrice)}`;
    } else {
        statusLabel.textContent = '✅ UNTUNG';
        statusLabel.className = 'text-green-500 font-bold text-[10px]';
        profitBar.className = 'h-full bg-green-500 transition-all duration-500';
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
