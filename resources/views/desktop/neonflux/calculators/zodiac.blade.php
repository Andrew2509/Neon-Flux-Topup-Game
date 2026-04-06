@extends('desktop.layouts.neonflux')

@section('title', 'Kalkulator Zodiac - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pt-32 pb-20 relative overflow-hidden isolate">
    <!-- Background Decor -->
    <div class="absolute top-1/4 -left-20 size-96 bg-amber-500/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-primary/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" style="animation-delay: 2s" aria-hidden="true"></div>

    <div class="w-full max-w-2xl relative z-0">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-amber-500/10 text-amber-500 mb-4 border border-amber-500/20 shadow-lg shadow-amber-500/5">
                    <span class="material-icons-round text-3xl">stars</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-rose-100/90 tracking-tight flex items-center justify-center gap-4">
                    <span class="text-amber-500">Kalkulator</span> Zodiac 
                </h1>
                <p class="text-slate-400 font-medium text-sm">Hitung diamond maksimal untuk mendapatkan Skin Zodiac idaman Anda.</p>
            </div>

            <!-- Form -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Star Power Saat Ini</label>
                    <div class="relative group">
                        <input type="number" id="currentStar" placeholder="Contoh: 80" max="100" min="0"
                               class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xl font-bold text-white outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-500 font-bold">/ 100</span>
                    </div>
                </div>
                
                <button type="button" id="btnHitung" class="w-full bg-amber-500 text-slate-950 font-black text-lg py-5 rounded-2xl shadow-xl shadow-amber-500/20 hover:shadow-amber-500/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <span>Lihat Perhitungan</span>
                    <span class="material-icons-round">auto_awesome</span>
                </button>
            </div>

            <!-- Result Section -->
            <div id="resultBox" class="hidden mt-12 pt-10 border-t border-white/5 animate-in fade-in slide-in-from-bottom-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 text-center space-y-2">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sisa Diamond</p>
                        <h2 class="text-3xl font-black text-amber-400" id="remDiamond">0</h2>
                    </div>
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 text-center space-y-2">
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Sisa Rupiah</p>
                        <h2 class="text-3xl font-black text-white" id="remRupiah">Rp 0</h2>
                    </div>
                </div>
                <div class="mt-4 bg-linear-to-r from-amber-500/20 to-transparent border border-amber-500/20 rounded-3xl p-6">
                   <p class="text-sm font-medium text-slate-300">
                       Dibutuhkan sekitar <span class="text-amber-400 font-bold" id="drawCount">0</span> kali draw lagi untuk mencapai <span class="text-amber-400 font-bold">100</span> Star Power.
                   </p>
                </div>
            </div>

            <div class="mt-8 p-4 bg-white/5 border border-white/10 rounded-2xl text-[10px] text-slate-500 text-center leading-relaxed italic">
                *Star Power per draw bervariasi (1-5). Simulasi ini menggunakan angka aman rata-rata diamond per 1 start power.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnHitung').addEventListener('click', function() {
    const current = parseInt(document.getElementById('currentStar').value);

    if (isNaN(current) || current < 0 || current > 100) {
        Swal.fire({ icon: 'error', title: 'Oops!', text: 'Star Power 0-100', background: '#0a0a15', color: '#fff' });
        return;
    }

    const needed = 100 - current;
    // Average 1 draw (20 diamond) gives approx 1 star? (Conservative)
    const totalDiamond = needed * 20; 
    const totalRupiah = totalDiamond * 250; 

    document.getElementById('remDiamond').innerText = totalDiamond.toLocaleString();
    document.getElementById('remRupiah').innerText = 'Rp ' + totalRupiah.toLocaleString();
    document.getElementById('drawCount').innerText = Math.ceil(needed / 1.5);
    
    document.getElementById('resultBox').classList.remove('hidden');
});
</script>
@endpush
@endsection
