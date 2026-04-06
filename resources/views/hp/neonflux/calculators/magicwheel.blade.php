@extends('hp.layouts.neonflux')

@section('title', 'Kalkulator Magic Wheel')

@section('content')
<div class="space-y-6 pb-20 relative z-0 isolate">
    <!-- Header Card -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm overflow-hidden relative text-center space-y-3">
        <div class="absolute -top-10 -right-10 size-40 bg-purple-500/10 blur-3xl rounded-full"></div>
        <div class="size-14 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center border border-purple-500/20 mx-auto shadow-sm">
            <span class="material-icons-round text-3xl">auto_fix_high</span>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kalkulator Magic Wheel</h1>
        <p class="text-slate-500 text-[10px] font-bold leading-relaxed px-4">Hitung sisa diamond untuk mendapatkan Skin Legends idaman.</p>
    </div>

    <!-- Form -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm space-y-6">
        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Magic Point Saat Ini (0-200)</label>
            <div class="relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">hotel_class</span>
                <input type="number" id="currentPoint" placeholder="Contoh: 180" max="200" min="0"
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-purple-500 outline-none">
            </div>
        </div>

        <button type="button" id="btnHitung" class="w-full bg-purple-500 text-white font-black text-sm py-5 rounded-2xl shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span>Estimasi Biaya</span>
            <span class="material-icons-round text-lg">monetization_on</span>
        </button>
    </div>

    <!-- Results Overview -->
    <div id="resultBox" class="hidden animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-900 p-5 rounded-3xl text-center space-y-1 flex flex-col items-center justify-center">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Sisa Point</p>
                <h2 class="text-2xl font-black text-white" id="remPoint">0</h2>
            </div>
            <div class="bg-primary/10 border border-primary/20 p-5 rounded-3xl text-center space-y-1 flex flex-col items-center justify-center">
                <p class="text-[9px] font-black text-primary uppercase tracking-widest leading-none">Sisa Diamond</p>
                <h2 class="text-2xl font-black text-primary" id="remDiamond">0</h2>
            </div>
        </div>
        
        <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100 flex items-center justify-between">
            <div class="text-left">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1">Rupiah Dibutuhkan</p>
                <p class="text-[8px] text-slate-500 font-bold">Asumsi Rp 250 / Diamond</p>
            </div>
            <h3 class="text-xl font-black text-emerald-600" id="remRupiah">Rp 0</h3>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnHitung').addEventListener('click', function() {
    const current = parseInt(document.getElementById('currentPoint').value);

    if (isNaN(current) || current < 0 || current > 200) {
        Swal.fire({ icon: 'error', title: 'Ups!', text: 'Point harus di antara 0 - 200', position: 'center', timer: 2000, showConfirmButton: false });
        return;
    }

    const needed = 200 - current;
    const totalDiamond = Math.ceil(needed / 5) * 270;
    const totalRupiah = totalDiamond * 250;

    document.getElementById('remPoint').innerText = needed;
    document.getElementById('remDiamond').innerText = totalDiamond.toLocaleString();
    document.getElementById('remRupiah').innerText = 'Rp ' + totalRupiah.toLocaleString();
    
    document.getElementById('resultBox').classList.remove('hidden');
    
    setTimeout(() => {
        document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
});
</script>
@endpush
@endsection
