@extends('hp.layouts.neonflux')

@section('title', 'Kalkulator Win Rate')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Header Card -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm overflow-hidden relative text-center space-y-3">
        <div class="absolute -top-10 -left-10 size-32 bg-primary/10 blur-3xl rounded-full"></div>
        <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 mx-auto shadow-sm">
            <span class="material-icons-round text-3xl">calculate</span>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kalkulator WR</h1>
        <p class="text-slate-500 text-[10px] font-bold leading-relaxed px-4">Estimasi jumlah kemenangan beruntun (Win Streak) untuk menaikkan win rate Anda.</p>
    </div>

    <!-- Multi-Step Form -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm space-y-6">
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Total Match Saat Ini</label>
                <div class="relative">
                    <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">videogame_asset</span>
                    <input type="number" id="totalMatch" placeholder="Contoh: 1000"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Win Rate Saat Ini (%)</label>
                <div class="relative">
                    <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">query_stats</span>
                    <input type="number" id="totalWr" placeholder="Contoh: 50.4" step="0.01"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Target Win Rate (%)</label>
                <div class="relative">
                    <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">flag</span>
                    <input type="number" id="targetWr" placeholder="Contoh: 60" step="0.01"
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>
        </div>

        <button type="button" id="btnHitung" class="w-full bg-primary text-slate-900 font-black text-sm py-5 rounded-2xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span>Hitung Sekarang</span>
            <span class="material-icons-round text-lg">trending_up</span>
        </button>
    </div>

    <!-- Result Banner -->
    <div id="resultBox" class="hidden animate-in fade-in zoom-in-95 duration-500">
        <div class="bg-linear-to-br from-primary/10 to-primary/5 border border-primary/20 rounded-3xl p-8 text-center space-y-3 relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 size-40 bg-primary/10 blur-3xl rounded-full"></div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Minimal Harus Menang</p>
            <h2 class="text-6xl font-black text-primary tracking-tighter" id="resultValue">0</h2>
            <p class="text-sm font-black text-slate-900">Win Tanpa Kalah</p>
            <p class="text-[10px] font-bold text-slate-400 leading-none">Untuk target win rate <span id="targetPlaceholder" class="text-primary font-bold">0</span>%</p>
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
        Swal.fire({ icon: 'error', title: 'Oops!', text: 'Harap isi semua kolom.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        return;
    }

    if (targetWr > 100 || targetWr <= tWr) {
        Swal.fire({ icon: 'warning', title: 'Perhatian!', text: 'Input target win rate yang masuk akal.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        return;
    }

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
