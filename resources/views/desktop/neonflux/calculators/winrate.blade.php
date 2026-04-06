@extends('desktop.layouts.neonflux')

@section('title', 'Kalkulator Win Rate - ' . get_setting('site_name', 'NEON FLUX'))

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-start px-4 pt-[calc(var(--header-h)+1.25rem)] sm:pt-40 pb-24 md:pb-28 relative overflow-x-hidden isolate scroll-mt-24">
    <!-- Background Decor (di belakang kartu; hindari z-10 pada kartu = menutup drawer/nav) -->
    <div class="absolute top-1/4 -left-20 size-96 bg-primary/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute bottom-1/4 -right-20 size-80 bg-cyan-500/10 blur-[120px] rounded-full animate-pulse -z-10 pointer-events-none" style="animation-delay: 2s" aria-hidden="true"></div>

    <div class="w-full max-w-2xl relative z-0">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-primary/10 text-primary mb-4 border border-primary/20 shadow-lg shadow-primary/5">
                    <span class="material-icons-round text-3xl">calculate</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Kalkulator Win Rate</h1>
                <p class="text-slate-400 font-medium text-sm">Hitung jumlah match yang harus dimenangkan untuk mencapai target win rate.</p>
            </div>

            <!-- Form -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Total Match Anda</label>
                        <div class="relative group">
                            <input type="number" id="totalMatch" placeholder="Contoh: 1000"
                                   class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-base font-bold text-white outline-none focus:ring-2 focus:ring-primary transition-all">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Total Win Rate (%)</label>
                        <div class="relative group">
                            <input type="number" id="totalWr" placeholder="Contoh: 50.5" step="0.01"
                                   class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-base font-bold text-white outline-none focus:ring-2 focus:ring-primary transition-all">
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Target Win Rate (%)</label>
                    <div class="relative group">
                        <input type="number" id="targetWr" placeholder="Contoh: 60" step="0.01"
                               class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-base font-bold text-white outline-none focus:ring-2 focus:ring-primary transition-all">
                    </div>
                </div>
                
                <button type="button" id="btnHitung" class="w-full bg-primary text-slate-950 font-black text-lg py-5 rounded-2xl shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <span>Mulai Hitung</span>
                    <span class="material-icons-round">trending_up</span>
                </button>
            </div>

            <!-- Result Section -->
            <div id="resultBox" class="hidden mt-12 pt-10 border-t border-white/5 space-y-6 animate-in fade-in slide-in-from-bottom-5">
                <div class="bg-primary/5 border border-primary/20 rounded-3xl p-8 text-center space-y-4">
                    <p class="text-slate-400 font-medium">Anda memerlukan sekitar</p>
                    <h2 class="text-5xl font-black text-primary tracking-tighter" id="resultValue">0</h2>
                    <p class="text-lg font-bold text-white">Win Tanpa Lose</p>
                    <p class="text-sm text-slate-500">Untuk mencapai target win rate <span id="targetPlaceholder" class="text-primary font-bold">0</span>%</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnHitung').addEventListener('click', function() {
    const tMatch = parseFloat(document.getElementById('totalMatch').value);
    const tWr = parseFloat(document.getElementById('totalWr').value);
    const targetWr = parseFloat(document.getElementById('targetWr').value);

    if (isNaN(tMatch) || isNaN(tWr) || isNaN(targetWr)) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Harap isi semua field dengan angka valid!',
            background: '#0a0a15',
            color: '#fff'
        });
        return;
    }

    if (targetWr > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Wow!',
            text: 'Mencapai win rate di atas 100% itu mustahil, kawan.',
            background: '#0a0a15',
            color: '#fff'
        });
        return;
    }

    if (targetWr <= tWr) {
        Swal.fire({
            icon: 'info',
            title: 'Selamat!',
            text: 'Win rate Anda saat ini sudah mencapai atau melebihi target.',
            background: '#0a0a15',
            color: '#fff'
        });
        return;
    }

    // Formula: (targetWr/100 * (tMatch + x)) = (tWr/100 * tMatch) + x
    // targetWr/100 * tMatch + targetWr/100 * x = tWr/100 * tMatch + x
    // x - (targetWr/100 * x) = (targetWr/100 * tMatch) - (tWr/100 * tMatch)
    // x(1 - targetWr/100) = (targetWr - tWr)/100 * tMatch
    // x = ((targetWr - tWr)/100 * tMatch) / (1 - targetWr/100)
    // x = (targetWr - tWr) * tMatch / (100 - targetWr)

    const winNeeded = Math.ceil((targetWr - tWr) * tMatch / (100 - targetWr));

    document.getElementById('resultValue').innerText = winNeeded.toLocaleString();
    document.getElementById('targetPlaceholder').innerText = targetWr;
    document.getElementById('resultBox').classList.remove('hidden');
    
    // Smooth scroll to result
    setTimeout(() => {
        document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
});
</script>
@endpush
@endsection
