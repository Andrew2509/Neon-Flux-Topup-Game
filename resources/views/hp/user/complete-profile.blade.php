@extends('hp.layouts.neonflux')

@section('title', 'Lengkapi Profil - ' . get_setting('site_name', 'Neon Flux'))

@section('content')
<div class="mb-10">
    <div class="glass-panel-mobile p-6 rounded-3xl border border-slate-200 bg-white">
        <div class="flex flex-col items-center text-center mb-8">
            <div class="size-16 rounded-2xl bg-cyan-600 text-white flex items-center justify-center shadow-lg shadow-cyan-100 mb-4">
                <span class="material-icons-round text-3xl">whatsapp</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Sedikit Lagi!</h2>
            <p class="text-slate-500 text-xs font-medium mt-1">Silakan lengkapi nomor WhatsApp Anda untuk kemudahan pengiriman bukti transaksi.</p>
        </div>

        <form action="{{ route('profile.complete.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-900 font-bold text-base">+62</span>
                    </div>
                    <input type="number" name="phone" value="{{ old('phone') }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-14 pr-4 py-4 text-base font-bold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 outline-none transition-all placeholder:text-slate-300"
                           placeholder="81234567890" required autofocus>
                </div>
                @error('phone')
                    <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl">
                <p class="text-[10px] text-amber-700 font-medium leading-relaxed">
                    <strong>Penting:</strong> Pastikan nomor sudah benar agar notifikasi pesanan sampai ke WhatsApp Anda.
                </p>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl active:scale-95 transition-all text-sm uppercase tracking-wider">
                Simpan & Lanjutkan
            </button>
        </form>
    </div>
</div>
@endsection
