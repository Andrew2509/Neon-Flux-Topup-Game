@extends('desktop.layouts.user')

@section('title', 'Lengkapi Profil — ' . get_setting('site_name', 'Neon Core'))
@section('page_title', 'Lengkapi Profil')
@section('page_subtitle', 'Sedikit lagi! Silakan lengkapi nomor WhatsApp Anda untuk kemudahan transaksi.')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="content-card p-8 md:p-10 rounded-2xl border border-corp-border space-y-8 bg-white shadow-xl relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
        
        <div class="relative">
            <div class="flex items-center gap-5 border-b border-corp-border pb-8">
                <div class="size-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-corp-navy tracking-tight">Nomor WhatsApp</h2>
                    <p class="text-corp-muted text-sm font-medium mt-1">Digunakan untuk mengirim bukti bayar & notifikasi.</p>
                </div>
            </div>
            
            <form action="{{ route('profile.complete.store') }}" method="POST" class="mt-10 space-y-8">
                @csrf
                
                <div class="space-y-3">
                    <label class="block text-xs font-black text-corp-muted uppercase tracking-[0.2em] ml-1">Nomor WhatsApp (Aktif)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <span class="text-corp-navy font-black text-lg">+62</span>
                        </div>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="w-full bg-slate-50 border-2 border-corp-border rounded-2xl pl-16 pr-6 py-5 text-xl font-black text-corp-navy focus:border-blue-600 focus:bg-white outline-none transition-all placeholder:text-slate-300 shadow-sm"
                               placeholder="81234567890" required autofocus>
                    </div>
                    @error('phone')
                        <p class="text-red-500 text-xs font-bold mt-2 ml-1 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl flex gap-4">
                    <div class="shrink-0 text-amber-500 mt-0.5">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 01-1-1v-4a1 1 0 112 0v4a1 1 0 01-1 1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-[11px] text-amber-800 font-bold leading-relaxed uppercase tracking-tight">
                        Pastikan nomor sudah benar. Kami akan mengirimkan notifikasi transaksi ke nomor ini setiap kali Anda berbelanja.
                    </p>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 text-sm uppercase tracking-[0.2em] transform active:scale-[0.98]">
                    Konfirmasi & Lanjutkan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
