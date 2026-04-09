@extends('admin.layouts.app')

@section('title', 'Edit Voucher')
@section('page_title', 'Edit Voucher')
@section('page_description', 'Perbarui detail dan syarat penggunaan voucher promo.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.vouchers') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Kelola Voucher</span>
    </a>

    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-8 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 size-64 bg-secondary/10 blur-[100px] rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Promo -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">local_activity</span>
                        Informasi Promo
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">KODE VOUCHER</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">qr_code</span>
                                <input type="text" name="code" required placeholder="Contoh: PROMOAWALYTAHUN" value="{{ old('code', $voucher->code) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono uppercase">
                            </div>
                            @error('code') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">TIPE POTONGAN</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="nominal" {{ old('type', $voucher->type) == 'nominal' ? 'checked' : '' }} class="peer hidden">
                                    <div class="flex items-center justify-center gap-2 p-3 rounded-2xl bg-white/5 border border-white/10 text-xs font-bold text-slate-400 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary transition-all">
                                        <span class="material-symbols-outlined text-sm">payments</span>
                                        Nominal (Rp)
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="percentage" {{ old('type', $voucher->type) == 'percentage' ? 'checked' : '' }} class="peer hidden">
                                    <div class="flex items-center justify-center gap-2 p-3 rounded-2xl bg-white/5 border border-white/10 text-xs font-bold text-slate-400 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary transition-all">
                                        <span class="material-symbols-outlined text-sm">percent</span>
                                        Persentase (%)
                                    </div>
                                </label>
                            </div>
                            @error('type') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1" id="discount_label">NILAI POTONGAN</label>
                            <div class="relative group">
                                <span id="discount_prefix" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold group-focus-within:text-accent-blue transition-colors">Rp</span>
                                <input type="number" name="discount_amount" id="discount_amount" required placeholder="10000" value="{{ old('discount_amount', $voucher->discount_amount) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-12 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                                <span id="discount_suffix" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold opacity-0 transition-opacity">%</span>
                            </div>
                            @error('discount_amount') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">MINIMAL PEMBELIAN (RP)</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-bold group-focus-within:text-accent-blue transition-colors">Rp</span>
                                <input type="number" name="min_purchase" required placeholder="50000" value="{{ old('min_purchase', $voucher->min_purchase) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                            </div>
                            @error('min_purchase') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Syarat & Ketentuan -->
                <div class="space-y-6">
                    <h3 class="text-sm font-bold text-accent-blue uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">gavel</span>
                        Syarat & Ketentuan
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 ml-1">KUOTA PENGGUNAAN</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">group</span>
                                    <input type="number" name="quota" required placeholder="100" value="{{ old('quota', $voucher->quota) }}"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                                </div>
                                @error('quota') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 ml-1">STATUS</label>
                                <select name="status" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all appearance-none cursor-pointer">
                                    <option value="Aktif" {{ old('status', $voucher->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Nonaktif" {{ old('status', $voucher->status) == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 ml-1">BATAS WAKTU (OPSIONAL)</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">event</span>
                                <input type="date" name="expiry_date" value="{{ old('expiry_date', $voucher->expiry_date ? \Carbon\Carbon::parse($voucher->expiry_date)->format('Y-m-d') : '') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm text-slate-300 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('expiry_date') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.vouchers') }}" class="px-6 py-3 rounded-2xl text-sm font-bold text-slate-400 hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="bg-secondary text-black px-8 py-3 rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-secondary/20 transition-all active:scale-95">
                    Perbarui Voucher
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeInputs = document.querySelectorAll('input[name="type"]');
        const prefix = document.getElementById('discount_prefix');
        const suffix = document.getElementById('discount_suffix');
        const input = document.getElementById('discount_amount');
        const label = document.getElementById('discount_label');

        function updateUI() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            if (selectedType === 'percentage') {
                prefix.style.opacity = '0';
                suffix.style.opacity = '1';
                input.placeholder = '10';
                label.innerText = 'PERSENTASE DISKON (%)';
            } else {
                prefix.style.opacity = '1';
                suffix.style.opacity = '0';
                input.placeholder = '10000';
                label.innerText = 'NOMINAL DISKON (RP)';
            }
        }

        typeInputs.forEach(input => {
            input.addEventListener('change', updateUI);
        });

        // Run on load
        updateUI();
    });
</script>
@endpush
