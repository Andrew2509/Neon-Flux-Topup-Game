@extends('hp.layouts.neonflux')

@section('title', 'Kalkulator Zodiac')

@section('content')
<div class="space-y-6 pb-20">
    <!-- Header Card -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm overflow-hidden relative text-center space-y-3">
        <div class="absolute -top-10 -right-10 size-40 bg-amber-500/10 blur-3xl rounded-full"></div>
        <div class="size-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center border border-amber-500/20 mx-auto shadow-lg shadow-amber-500/20">
            <span class="material-icons-round text-3xl font-bold">stars</span>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none pt-2">Kalkulator Zodiac</h1>
        <p class="text-slate-500 text-[10px] font-bold leading-relaxed px-4">Hitung jumlah diamond yang Anda butuhkan untuk mendapatkan Skin Zodiac idaman.</p>
    </div>

    <!-- Form -->
    <div class="glass-panel-mobile p-6 rounded-3xl border-slate-200 shadow-sm space-y-6">
        <div class="space-y-1.5">
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Star Power Saat Ini (0-100)</label>
            <div class="relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">auto_awesome</span>
                <input type="number" id="currentStar" placeholder="Contoh: 75" max="100" min="0" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
            </div>
        </div>

        <button type="button" id="btnHitung" class="w-full bg-amber-500 text-slate-950 font-black text-sm py-5 rounded-2xl shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 active:scale-95 transition-all">
            <span>Estimasi Biaya</span>
            <span class="material-icons-round text-lg">savings</span>
        </button>
    </div>

    <!-- Results Overview -->
    <div id="resultBox" class="hidden space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-amber-50 p-5 rounded-3xl text-center flex flex-col items-center justify-center text-amber-600 border border-amber-100">
                <p class="text-[9px] font-black uppercase tracking-widest leading-none mb-1">Star Power Sisa</p>
                <h2 class="text-2xl font-black" id="remStar">0</h2>
            </div>
            <div class="bg-slate-900 p-5 rounded-3xl text-center space-y-1 flex flex-col items-center justify-center">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Diamond Sisa</p>
                <h2 class="text-2xl font-black text-white" id="remDiamond">0</h2>
            </div>
        </div>
        
        <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100 flex flex-col items-center gap-1">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1">Rupiah Dibutuhkan</p>
            <h3 class="text-3xl font-black text-emerald-600" id="remRupiah">Rp 0</h3>
            <p class="text-[8px] text-slate-500 font-bold">Asumsi Rp 250 / Diamond</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnHitung').addEventListener('click', function() {
    const current = parseInt(document.getElementById('currentStar').value);

    if (isNaN(current) || current < 0 || current > 100) {
        Swal.fire({ icon: 'error', title: 'Ups!', text: 'Star Power 0 - 100', position: 'center', timer: 2000, showConfirmButton: false });
        return;
    }

    const needed = 100 - current;
    const totalDiamond = needed * 20; 
    const totalRupiah = totalDiamond * 250;

    document.getElementById('remStar').innerText = needed;
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
