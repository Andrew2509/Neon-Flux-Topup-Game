@extends('desktop.layouts.neonflux')

@section('title', 'Kalkulator Magic Wheel - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-start px-4 pt-[calc(var(--header-h)+1.25rem)] sm:pt-40 pb-24 md:pb-28 relative overflow-x-hidden isolate scroll-mt-24">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-purple-500/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-primary/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" style="animation-delay: 2s" aria-hidden="true"></div>

    <div class="w-full max-w-2xl relative z-0">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-primary/10 text-primary mb-4 border border-primary/20 shadow-lg shadow-primary/5">
                    <span class="material-icons-round text-3xl">auto_fix_high</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Kalkulator Magic Wheel</h1>
                <p class="text-slate-400 font-medium text-sm">Estimasi jumlah diamond yang dibutuhkan untuk mendapatkan Skin Legends.</p>
            </div>

            <!-- Form -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Magic Point Saat Ini</label>
                    <div class="relative group">
                        <input type="number" id="currentPoint" placeholder="Contoh: 150" max="200" min="0"
                               class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xl font-bold text-white outline-none focus:ring-2 focus:ring-primary transition-all">
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-500 font-bold">/ 200</span>
                    </div>
                </div>
                
                <button type="button" id="btnHitung" class="w-full bg-primary text-slate-950 font-black text-lg py-5 rounded-2xl shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <span>Estimasi Biaya</span>
                    <span class="material-icons-round">payments</span>
                </button>
            </div>

            <!-- Result Section -->
            <div id="resultBox" class="hidden mt-12 pt-10 border-t border-white/5 animate-in fade-in slide-in-from-bottom-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 text-center space-y-2">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sisa Point</p>
                        <h2 class="text-3xl font-black text-white" id="remPoint">0</h2>
                    </div>
                    <div class="bg-primary/10 border border-primary/20 rounded-3xl p-6 text-center space-y-2">
                        <p class="text-[10px] font-black text-primary uppercase tracking-widest">Sisa Diamond</p>
                        <h2 class="text-3xl font-black text-primary" id="remDiamond">0</h2>
                    </div>
                </div>
                <div class="mt-4 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-6 flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Estimasi Rupiah</p>
                        <p class="text-xs text-slate-400">Asumsi Rp 250 / Diamond</p>
                    </div>
                    <h2 class="text-2xl font-black text-emerald-400" id="remRupiah">Rp 0</h2>
                </div>
            </div>

            <div class="mt-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-start gap-4">
                <span class="material-icons-round text-amber-500 mt-0.5">info</span>
                <p class="text-[10px] text-amber-200/70 font-medium leading-relaxed">
                    *Estimasi menggunakan harga standard (5x Draw = 270 Diamond). 
                    Harga sebenarnya mungkin lebih murah jika menggunakan diskon harian atau promo tertentu.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnHitung').addEventListener('click', function() {
    const current = parseInt(document.getElementById('currentPoint').value);

    if (isNaN(current) || current < 0 || current > 200) {
        Swal.fire({ icon: 'error', title: 'Invalid!', text: 'Point harus di antara 0 - 200', background: '#0a0a15', color: '#fff' });
        return;
    }

    const needed = 200 - current;
    const diamondPer5 = 270;
    const totalDiamond = Math.ceil(needed / 5) * diamondPer5;
    const totalRupiah = totalDiamond * 250; // Average price

    document.getElementById('remPoint').innerText = needed;
    document.getElementById('remDiamond').innerText = totalDiamond.toLocaleString();
    document.getElementById('remRupiah').innerText = 'Rp ' + totalRupiah.toLocaleString();
    
    document.getElementById('resultBox').classList.remove('hidden');
});
</script>
@endpush
@endsection
